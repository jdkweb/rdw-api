<?php

namespace Jdkweb\Rdw\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Lang;
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Exceptions\RdwException;

class OverheidIO extends Rdw implements RdwApi
{
    protected string $base_uri = "https://api.overheid.io";

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
                'Accept' => 'application/json',
                'ovio-api-key' => config("rdw-api.rdw_api_key"),
            ]
        ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fetch the Rdw results
     *
     * @return string|array
     * @throws RdwException
     */
    final public function fetch():string|array
    {
        $response = $this->getRequest();

        // Split result into rdw datasets
        $result = $this->toDataSets($response);

        foreach ($this->endpoints as $endpoint) {
            // Check endpoint exists
            if (!$endpoint instanceof Endpoints) {
                continue;
            }

            // Remove specific overheid.io data-fields
            $result[$endpoint->name] = array_filter($result[$endpoint->name], function ($key) {
                return (substr($key, 0, 1) !== ":") && $key !== "_links";
            }, ARRAY_FILTER_USE_KEY);

            $this->result[$endpoint->name] = $result[$endpoint->name];
        }

        // Translation when needed
        return $this->translateOutput($this->result);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Split data into RDW datasets
     *
     * @param  array  $response
     * @return array
     */
    final protected function toDataSets(array $response): array
    {
        $result = [];
        $result[Endpoints::FUEL->name] = $response['brandstof'];
        unset($response['brandstof']);
        $result[Endpoints::BODYWORK_SPECIFIC->name] = [];
        $result[Endpoints::VEHICLE_CLASS->name] = [];
        if (isset($response['carrosserie'][0]['specificatie'][0])) {
            $result[Endpoints::BODYWORK_SPECIFIC->name] = $response['carrosserie'][0]['specificatie'][0];
            unset($response['carrosserie'][0]['specificatie']);
        }
        if (isset($response['carrosserie'][0]['voertuigklasse'][0])) {
            $result[Endpoints::VEHICLE_CLASS->name] = $response['carrosserie'][0]['voertuigklasse'][0];
            unset($response['carrosserie'][0]['voertuigklasse']);
        }
        $result[Endpoints::BODYWORK->name] = $response['carrosserie'][0];
        unset($response['carrosserie']);
        $result[Endpoints::AXLES->name] = $response['as'];
        unset($response['as']);
        $result[Endpoints::VEHICLE->name] = $response;

        return $result;
    }

    //------------------------------------------------------------------------------------------------------------------

    final protected function getRequest(): ?array
    {
        try {
            $response = $this->client->get("/voertuiggegevens/" . $this->license);
            $statusCode = $response->getStatusCode() ?? 404;

            $responseBody = (string) $response->getBody();

            $result = json_decode($responseBody, true) ?? [];

            if (count($result) > 1) {
                return $result;
            }

            return null;
        } catch (ClientException $e) {
            throw new RdwException(__('rdw-api::errors.endpoint_error', [
                'class' => self::class,
                'message' => $e->getMessage(),
                'endpoint_name' => 'nvt',
                'endpoint_value' => ''
            ]));
        }
    }
}
