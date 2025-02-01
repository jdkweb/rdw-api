<?php

namespace Jdkweb\Rdw\Api;

use GuzzleHttp\Client;
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Enums\OutputFormat;
use Jdkweb\Rdw\Exceptions\RdwException;
use Spatie\ArrayToXml\ArrayToXml;

abstract class Rdw
{
    /**
     * API output result in selected format
     * @var array | string
     */
    protected array $result = [];

    /**
     * Base uri for Api request
     * @var string
     */
    protected string $base_uri = '';

    /**
     * HTTP CLient
     * @var Client
     */
    protected Client $client;

    /**
     * Car numberplate
     * @var string|null
     */
    protected ?string $license = null;

    /**
     * RWD endpoints
     * @var array
     */
    protected array $endpoints = [];

    /**
     * Result output array | json | xml
     * @var OutputFormat
     */
    protected OutputFormat $output_format = OutputFormat::ARRAY;

    /**
     * API request method
     * @return mixed
     */
    abstract protected function fetch():string|array;

    public function __construct()
    {
        $this->setClient();

        // nl default language
        app()->setFallbackLocale('nl');
    }

    /**
     * Set HTTP Client
     *
     * @return void
     */
    final protected function setClient():void
    {
        $this->client = new Client([
            'base_uri' => rtrim($this->base_uri,"/")."/",
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
    }

    /**
     * Set and check for correct types (to call the endpoints)
     *
     * @param  array  $this->endpoints
     * @return Rdw
     */
    final public function setEndpoints(array $endpoints):Rdw
    {
        $result = $this->setAllEndpoints($endpoints) || $this->selectEndpoints($endpoints);

        return $this;
    }

    /**
     * Force other language
     *
     * @param  string  $language
     * @return Rdw
     */
    final public function translate(string $language):Rdw
    {
        app()->setLocale($language);

        return $this;
    }

    /**
     * Set output type
     *
     * @param  string  $type
     * @return Rdw
     */
    final public function format(string $type):Rdw
    {
        // Check type
        $res = array_filter(OutputFormat::cases(), function(OutputFormat $outputFormat) use($type){
            return ($outputFormat->name === strtoupper($type));
        });

        if(count($res) > 0) {
            $this->output_format = reset($res);
        }

        return $this;
    }

    /**
     * Validate and set Car-license
     *
     * @param  string  $license
     * @return Rdw
     * @throws RdwException
     */
    final public function setLicense(string $license):Rdw
    {
        $license = $this->formatLicense($license);

        if (strlen($license) !== 6) {
            throw new RdwException(__('rdw-api::errors.license', [
                'class' => self::class,
                'license' => $license
            ]));
        }

        $this->license = $license;

        return $this;
    }

    /**
     * Convert to XML
     *
     * @return string
     */
    final protected function toXml():string
    {
        return ArrayToXml::convert($this->result);
    }

    /**
     * Convert to Json
     *
     * @param $data
     * @return string
     */
    final protected function toJson(): string
    {
        return json_encode($this->result, true);
    }

    /**
     * Call convert
     *
     * @return string|array
     */
    final protected function convertOutput():string|array
    {
        return match ($this->output_format) {
            OutputFormat::XML => $this->toXml(),
            OutputFormat::JSON => $this->toJson(),
            default => $this->result,
        };
    }

    /**
     * Translate RDW result array
     *
     * @param  array  $result
     * @return array
     */
    final protected function translateOutput(array $result):array
    {
        $translation = [];
        if(app()->getLocale() == 'nl') {
            foreach ($result as $key=>$row) {
                $translation[__('rdw-api::enums.' . $key)] = $row;
            }
            return $translation;
        }

        foreach ($result as $key1=>$row1) {
            if(is_array($row1)) {
                foreach ($row1 as $key2=>$row2) {
                    if(is_array($row2)) {
                        foreach ($row2 as $key3=>$row3) {
                            $translation[__('rdw-api::enums.' . $key1)]
                                            [__('rdw-api::axles.as_nummer').($key2+1)]
                                                [__('rdw-api::' .  strtolower($key1) .".". $key3)] = $row3;
                        }
                    }
                    else {
                        $translation[__('rdw-api::enums.' . strtoupper($key1))]
                                        [__('rdw-api::' .  strtolower($key1) .".". $key2) ] = $row2;
                    }
                }
            }
            else {
                $translation[__('rdw-api::enums.' . $key1)] = [];
            }
        }

        return $translation;
    }


    /**
     * License [A-Z0-9] uppercase
     *
     * @param  string  $license
     * @return string
     */
    final protected function formatLicense(string $license): string
    {
        $license = preg_replace("/[^a-zA-Z0-9]+/", "", str_replace('-', '', $license));

        return strtoupper($license);
    }

    /**
     * Select 1 or more endpoints
     *
     * @param  array  $endpoints
     * @return bool
     */
    final protected function selectEndpoints(array $endpoints):bool
    {
        $this->endpoints = array_filter($endpoints, function ($type) {
            return in_array(strtoupper($type),Endpoints::names());
        });

        return count($this->endpoints) > 0;
    }

    /**
     * Select ALL form all Endpoints
     *
     * @param  array  $endpoints
     * @return bool
     */
    final protected function setAllEndpoints(array $endpoints): bool
    {
        if(count($endpoints) == 1 && strtoupper($endpoints[0]) == 'ALL') {
            $this->endpoints = Endpoints::names();
            return true;
        }

        return false;
    }
}
