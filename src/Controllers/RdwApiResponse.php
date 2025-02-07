<?php

namespace Jdkweb\Rdw\Controllers;

use Jdkweb\Rdw\Enums\Endpoints;
use Spatie\ArrayToXml\ArrayToXml;

class RdwApiResponse
{
    /**
     * Results from Rdw Api request
     * @var array
     */
    public array $response;

    /**
     * Request properties
     * @var object
     */
    public object $request;

    /**
     * Prepared output, when is set in request
     * @var array|string
     */
    public array|string $output;

    /**
     * Request status
     * @var bool
     */
    public bool $status;


    /**
     * Keys, always dutch key also when output is other language
     *
     * @param  string  $key
     * @param  int|null  $axle Axles multidimensional
     * @return string|null
     */
    final public function quickSearch(string $key, ?int $axle = null):?string
    {
        $endpoints = $this->request->endpoints;
        $lang = $this->request->language;

        // walkthrough endpoints
        foreach ($endpoints as $endpoint) {
            // Search for name
            $found = array_filter($this->response[$endpoint->getName()], function ($value, $index) use ($key, $axle) {
                if(is_null($axle)) {
                    return $index === $key;
                }
                elseif($index === $axle) {
                    // Search in axles sub-array
                    return array_filter($value, function ($value, $index) use ($key) {
                        return $index === $key;
                    },ARRAY_FILTER_USE_BOTH);
                }
            },ARRAY_FILTER_USE_BOTH);

            // Axles found get key => value
            if(count($found) > 0 && !is_null($axle) && $endpoint->name == 'AXLES') {
                $found = array_filter($found[$axle], fn ($value, $index) => $index === $key, ARRAY_FILTER_USE_BOTH);
            }

            // Check Word found
            if(count($found) != 1) continue;

            // Translation filename
            $name = strtolower($endpoint->name);

            $result = $this->response[$endpoint->getName()];
            if(!is_null($axle) && $endpoint->name == 'AXLES') {
                $result = $result[$axle];
            }
            return $result[__("rdw-api::".$name.".".$key)] ?? '';
        }

        return null;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Array result
     *
     * @return array
     */
    final public function toArray(): array|string
    {
        if(empty($this->response)) return '';

        return $this->response;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Array to object
     *
     * @param  array|null  $arr
     * @return object
     */
    final public function toObject(?array $arr = null): object
    {
        if(is_null($arr)) {
            $arr = $this->response;
        }

        if(empty($arr)) return (object)[];

        return (object) array_map(function($dataset) {
            if(is_array(reset($dataset))) {
                return $this->toObject($dataset);
            }
            return (object) $dataset;
        },$arr);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Convert to XML
     *
     * @return string
     */
    final public function toXml(bool $formatOutput = false): string
    {
        if(empty($this->response)) return '';

        $result = ArrayToXml::convert($this->response);
        if ($formatOutput) {
            return $this->formatXml($result);
        }
        return $result;
    }

    //------------------------------------------------------------------------------------------------------------------

    final protected function formatXml(string $result): string
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($result);
        return htmlentities($dom->saveXML($dom->documentElement));
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Convert to Json
     *
     * @param $data
     * @return string
     */
    final public function toJson(): string
    {
        if(empty($this->response)) return '';

        return json_encode($this->response, true);
    }
}
