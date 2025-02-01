<?php

namespace Jdkweb\Rdw\Api;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Jdkweb\Rdw\Exceptions\RdwException;
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Forms\Components\RdwApiResponse;

class OpendataRdw extends Rdw implements RdwApi
{
    protected string $base_uri = "https://opendata.rdw.nl/resource/";

    /**
     * Fetch the Rdw results
     *
     * @return string|array
     * @throws RdwException
     */
    final public function fetch():string|array
    {
        foreach ($this->endpoints as $type) {

            // Endpoint type
            $type = strtoupper($type);

            // Check endpoint exists
            if (!in_array($type,Endpoints::names())) {
                continue;
            }

            // Actual endpoint
            $endpoint = Endpoints::getCase($type);

            // Request
            $this->result[$endpoint->name] = $this->getRequest($endpoint) ?? [];
        }

        // Translation when needed
        $this->result = $this->translateOutput($this->result);

        // Output converted to type
        return $this->convertOutput();
    }

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
            if(count($result) == 1) {
                return json_decode($responseBody, true)[0] ?? [];
            }
            if(count($result) > 1) {
                return json_decode($responseBody, true) ?? [];
            }

            return null;

        } catch(ClientException $e) {
            throw new RdwException(__('rdw-api::errors.endpoint_error', [
                'class' => self::class,
                'message' => $e->getMessage(),
                'endpoint_name' => $endpoint->name,
                'endpoint_value' => $endpoint->value
            ]));
        }
    }

}
