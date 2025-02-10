<?php

namespace Jdkweb\Rdw\Controllers;

use Illuminate\Support\Facades\Lang;
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
    public array|string $output = '';

    /**
     * Request status
     * @var bool
     */
    public bool $status;


    /**
     * Keys, always dutch key also when output is other language
     *
     * @TODO rewrite
     *
     * @param  string  $key
     * @param  int|null  $axle Axles multidimensional
     * @return string|null
     */
    public function quickSearch(string $org_key, ?int $axle = null):?string
    {
        $local_lang = app()->getLocale();           // Website lang [en,nl]
        $endpoints = $this->request->endpoints;
        $lang = $this->request->language;           // Rdw wrapper language [en,nl]

        // walkthrough endpoints
        foreach ($endpoints as $endpoint) {
            $found = [];

            if($lang != 'nl' || $lang != $local_lang) {
                $key = Lang::get("rdw-api::".strtolower($endpoint->name).".".$org_key,[],$lang);
            }
            else {
                $key = $org_key;
            }


            if($lang != $local_lang) {
                $endpoint_key = Lang::get("rdw-api::enums.".$endpoint->name,[],$lang);
            }
            else {
                $endpoint_key = $endpoint->getName();
            }

            // Search for name
            if(!empty($this->response[$endpoint_key])) {
                $found = array_filter($this->response[$endpoint_key], function ($value, $index) use ($key, $axle) {
                    if (is_null($axle)) {
                        return $index === $key;
                    } elseif ($index === $axle) {
                        // Search in axles sub-array
                        return array_filter($value, function ($value, $index) use ($key) {
                            return $index === $key;
                        }, ARRAY_FILTER_USE_BOTH);
                    }
                }, ARRAY_FILTER_USE_BOTH);

            }
            // Axles found get key => value
            if (count($found) > 0 && !is_null($axle) && $endpoint->name == 'AXLES') {
                $found = array_filter($found[$axle], fn ($value, $index) => $index === $key, ARRAY_FILTER_USE_BOTH);
            }

            // Check Word found
            if (count($found) != 1) {
                continue;
            }

            // Translation filename
            $name = strtolower($endpoint->name);

            $result = $this->response[$endpoint_key];
            if (!is_null($axle) && $endpoint->name == 'AXLES') {
                $result = $result[$axle];
            }

            if($lang != 'nl' || $lang != $local_lang) {
                return $result[$key] ?? '';

            }
            else {
                return $result[__("rdw-api::".$name.".".$key)] ?? '';
            }
        }

        return null;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Array result
     *
     * @return array
     */
    public function toArray(): array|string
    {
        if (empty($this->response)) {
            return '';
        }

        return $this->response;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Array to object
     *
     * @param  array|null  $arr
     * @return object
     */
    public function toObject(?array $arr = null): object
    {
        if (is_null($arr)) {
            $arr = $this->response;
        }

        if (empty($arr)) {
            return (object)[];
        }

        return (object) array_map(function ($dataset) {
            if (is_array(reset($dataset))) {
                return $this->toObject($dataset);
            }
            return (object) $dataset;
        }, $arr);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Convert to XML
     *
     * Alt. formatXml
     * $arrayToXml = new ArrayToXml($array);
     * $arrayToXml->prettify()->toXml();
     *
     * @return string
     */
    public function toXml(bool $formatOutput = false): string
    {
        if (empty($this->response)) {
            return '';
        }

        $result = ArrayToXml::convert($this->response);
        if ($formatOutput) {
            return $this->formatXml($result);
        }
        return $result;
    }

    //------------------------------------------------------------------------------------------------------------------

    protected function formatXml(string $result): string
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
    public function toJson(): string
    {
        if (empty($this->response)) {
            return '';
        }

        return json_encode($this->response, true);
    }
}
