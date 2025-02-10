<?php

namespace Jdkweb\Rdw\Api;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Lang;
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Exceptions\RdwException;

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
    final public function setEndpoints(array $endpoints):static
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
    final public function setTranslation(string $language):static
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
    final public function setLicense(string $license):static
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
     * Translate RDW result array
     *
     * @param  array  $result
     * @return array
     */
    final protected function translateOutput(array $result): ?array
    {
        $translation = [];

        $skip = false;
        if (app()->getLocale() == $this->language && app()->getLocale() == 'nl') {
            $skip = true;
        }

        foreach ($result as $key => $row) {
            if (count($row) > 0) {
                $translation[Lang::get('rdw-api::enums.' . $key, [], $this->language)] = ($skip ? $row : []);
            }
        }

        if ($skip) {
            return $translation;
        }

        foreach ($result as $key1 => $row1) {
            if (is_array($row1)) {
                foreach ($row1 as $key2 => $row2) {
                    if (is_array($row2)) {
                        foreach ($row2 as $key3 => $row3) {
                            $translation
                            [Lang::get('rdw-api::enums.' . $key1, [], $this->language)]
                            [($key2)]   // [Lang::get('rdw-api::axles.as_nummer', [], $this->language) . ($key2+1)]
                            [Lang::get('rdw-api::' . strtolower($key1) .".". $key3, [], $this->language)] = $row3;
                        }
                    } else {
                        $translation
                        [Lang::get('rdw-api::enums.' . strtoupper($key1), [], $this->language)]
                        [Lang::get('rdw-api::' .  strtolower($key1) .".". $key2, [], $this->language)] = $row2;
                    }
                }
            } else {
                $translation[Lang::get('rdw-api::enums.' . $key1, [], $this->language)] = [];
            }
        }

        return (count($translation) == 0 ? null : $translation);
    }

    //------------------------------------------------------------------------------------------------------------------

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

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Select 1 or more endpoints
     *
     * @param  array  $endpoints array with strings or array with endpoints
     * @return bool
     */
    final protected function selectEndpoints(array $endpoints):bool
    {
        $this->endpoints =array_filter(array_map(function ($endpoint) {
            if (!$endpoint instanceof Endpoints && is_string($endpoint)) {
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
    final protected function setAllEndpoints(array $endpoints): bool
    {
        if (count($endpoints) == 1 && is_string($endpoints[0]) && strtoupper($endpoints[0]) == 'ALL') {
            $this->endpoints = Endpoints::cases();
            return true;
        }

        return false;
    }
}
