<?php

namespace Jdkweb\RdwApi\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Jdkweb\RdwApi\Exceptions\RdwException;
use Jdkweb\RdwApi\Enums\Endpoints;
use Jdkweb\RdwApi\Forms\Components\RdwApiResponse;

class OpendataRdw extends Rdw implements RdwApi
{
    protected string $base_uri = "https://opendata.rdw.nl/resource/";

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Set HTTP Client
     *
     * @return void
     */
    final public function setClient():void
    {
        $this->client = new Client([
            'base_uri' => rtrim($this->base_uri, "/")."/",
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fetch the Rdw results
     *
     * @return string|array|null
     * @throws RdwException
     */
    final public function fetch():string|array|null
    {
        foreach ($this->endpoints as $endpoint) {
            // Check endpoint exists
            if (!$endpoint instanceof Endpoints) {
                continue;
            }

            // Request
            $this->result[$endpoint->name] = $this->getRequest($endpoint) ?? [];
        }

        // Translation when needed
        return $this->translateOutput($this->result);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @param $endpoint
     * @return array|null
     * @throws RdwException
     * @throws GuzzleException
     */
    final protected function getRequest(Endpoints $endpoint): ?array
    {
        try {
            $response = $this->client->get($endpoint->value . "?kenteken=" . $this->license);
            $statusCode = $response->getStatusCode() ?? 404;

            $responseBody = (string) $response->getBody();

            $result = json_decode($responseBody, true);

            if (count($result) == 1) {
                return json_decode($responseBody, true)[0] ?? [];
            }
            if (count($result) > 1) {
                return json_decode($responseBody, true) ?? [];
            }

            return null;
        } catch (ClientException $e) {
            throw new RdwException(__('rdw-api::errors.endpoint_error', [
                'class' => self::class,
                'message' => $e->getMessage(),
                'endpoint_name' => $endpoint->name,
                'endpoint_value' => $endpoint->value
            ]));
        }
    }
}
