<?php

namespace Jdkweb\RdwApi\Api;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Lang;
use Jdkweb\RdwApi\Enums\Endpoints;
use Jdkweb\RdwApi\Enums\Interface\Endpoint;
use Jdkweb\RdwApi\Exceptions\RdwException;

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
     * RWD forced output language
     * @var string
     */
    protected string $language = '';


    /**
     * API request method
     * @return mixed
     */
    abstract protected function fetch():string|array|null;

    /**
     * Set HTTP Client for request
     * @return void
     */
    abstract protected function setClient():void;

    //------------------------------------------------------------------------------------------------------------------

    public function __construct()
    {
        $this->setClient();

        // default is All
        $this->endpoints = Endpoints::cases();

        // nl default language
        $this->language = app()->getLocale();
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Set and check for correct types (to call the endpoints)
     *
     * @param  array  $endpoints
     * @return Rdw
     */
    public function setEndpoints(array $endpoints):static
    {
        $this->endpoints = $endpoints;

        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Force output to other language then selected (or local)
     *
     * ex. app()->getLocal() = 'en'
     *     $this->language = 'nl'
     *     English form -> dutch data output
     *
     * @param  string  $language
     * @return Rdw
     */
    public function setTranslation(string $language):static
    {
        $this->language = $language;
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Validate and set Car-license
     *
     * @param  string  $license
     * @return Rdw
     * @throws RdwException
     */
    public function setLicense(string $license):static
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

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Translate RDW result array, walk through datasets
     *
     * @param  array  $result
     * @return array|null
     */
    protected function translateOutput(array $result): ?array
    {
        $translation = [
            'raw' => $result,
            'translated' => []
        ];

        foreach ($result as $dataset => $row) {
            $key = Lang::get('rdw-api::enums.' . $dataset, [], $this->language);
            if(count($row) > 0) {
                $translation['translated'][$key] = $this->translateDataSet($row, strtolower($dataset));
            }
        }

        return $translation;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Translate a RDW dataset
     *
     * @param  array  $array
     * @param  string  $dataset
     * @param  array  $key_map
     * @return array|null
     */
    protected function translateDataSet(array $array, string $dataset, array $key_map = []): ?array
    {
        $translation = [];

        // Rename key if found in the translation array
        foreach ($array as $key => $value) {
            // Sub array
            if (Lang::has('rdw-api::'.$dataset.".".$key, [], $this->language)) {
                $newKey = Lang::get('rdw-api::'.$dataset.".".$key, [], $this->language);
            }
            elseif(is_numeric($key)) {
                // cases like axles_1, axles_2 and fuel_1, fuel_2 (hybrid)
                $newKey = strtolower(Lang::get('rdw-api::enums.' . strtoupper($dataset), [], $this->language)) . "_". $key+1;
            }

            // not set, take original
            if(empty($newKey)) $newKey = $key;

            // If value is an array, recurse
            if (is_array($value)) {
                $translation[$newKey] = $this->translateDataSet($value, $dataset);
            } else {
                $translation[$newKey] = $value;
            }
        }

        return $translation;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * License [A-Z0-9] uppercase
     *
     * @param  string  $license
     * @return string
     */
    protected function formatLicense(string $license): string
    {
        $license = preg_replace("/[^a-zA-Z0-9]+/", "", str_replace('-', '', $license));

        return strtoupper($license);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Select 1 or more endpoints
     *
     * @param  array  $endpoints array with strings or array with endpoints
     * @return bool
     */
    protected function selectEndpoints(array $endpoints):bool
    {
        $this->endpoints =array_filter(array_map(function ($endpoint) {
            if (!$endpoint instanceof Endpoint && is_string($endpoint)) {
                return Endpoints::getCase($endpoint);
            } else {
                return $endpoint;
            }
        }, $endpoints), fn($endpoint) => in_array($endpoint, Endpoints::cases()));

        return count($this->endpoints) > 0;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Select ALL form all Endpoints
     *
     * @param  array  $endpoints array with strings or array with endpoints
     * @return bool
     */
    protected function setAllEndpoints(array $endpoints): bool
    {
        if (count($endpoints) == 1 && is_string($endpoints[0]) && strtoupper($endpoints[0]) == 'ALL') {
            $this->endpoints = Endpoints::cases();
            return true;
        }

        return false;
    }
}
