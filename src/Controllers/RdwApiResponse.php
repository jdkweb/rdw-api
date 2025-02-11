<?php

namespace Jdkweb\Rdw\Controllers;

use Illuminate\Support\Facades\Lang;
use Jdkweb\Rdw\Enums\Endpoints;
use Spatie\ArrayToXml\ArrayToXml;

class RdwApiResponse
{
    /**
     * Raw not translated RDW data
     *
     * @var array
     */
    public array $raw;

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

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Keys, always dutch key also when output is other language
     * walk through datasets
     *
     * @param  string  $key
     * @param  int|null  $subarray Fuel/Axles multidimensional
     * @return string|null
     */
    public function quickSearch(string $request_dutch_key):?string
    {
        // specific search on first or second axle
        // (exp. first axle) 1.wettelijk_toegestane_maximum_aslast
        $rowkey = null;

        if (preg_match("/^[0-9]{1}\.(.*)$/", $request_dutch_key)) {
            $rowkey = substr($request_dutch_key, 0, 1);
            $request_dutch_key = substr($request_dutch_key, 2);
        }

        foreach ($this->raw as $endpoint_name => $data) {
            if($word = $this->search_key_in_array($request_dutch_key, $data, $rowkey)) {
                return $word;
            }
        }

        return null;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Search for value by key in the dataset
     *
     * @param  string  $searchKey
     * @param  array|null  $search_array
     * @param  int|null  $rowkey
     * @return string|null
     */
    protected function search_key_in_array(string $searchKey, ?array $search_array = null, ?int $rowkey = null): ?string
    {
        foreach ($search_array as $key => $value) {
            if ($key === $searchKey) {
                return $value;
            }

            if (!is_null($rowkey) && is_array($value) && $key !== ($rowkey-1)) {
                continue;
            }

            if (is_array($value)) {
                if ($this->search_key_in_array($searchKey, $value)) {
                    return $value[$searchKey];
                }
            }
        }
        return null; // Key not found
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
