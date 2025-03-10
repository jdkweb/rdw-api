<?php

namespace Jdkweb\RdwApi\Controllers;

use Jdkweb\RdwApi\Enums\Endpoints;
use Jdkweb\RdwApi\Enums\OutputFormat;
use Jdkweb\RdwApi\Facades\Rdw;

class RdwApiRequest
{
    /**
     * Values for API request
     * @var string|null
     */
    protected int $api = 0;
    protected ?string $licenseplate = null;
    protected ?array $endpoints = null;
    protected string $language;
    protected ?OutputFormat $outputformat = null;

    /**
     * Result API request
     * @var array|string|null
     */
    private array|string|null $result = null;

    /**
     * @var RdwApiRequest|null
     */
    private static RdwApiRequest|null $instance = null;

    //------------------------------------------------------------------------------------------------------------------

    public static function make(): static
    {
        // Singleton
        if (is_null(self::$instance)) {
            self::$instance = new self();
            // Default settings
            self::$instance->endpoints = Endpoints::cases();
            self::$instance->language = app()->getLocale();
        }

        return self::$instance;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Call to Rdw API
     *
     * @return array|string
     */
    public function fetch(?bool $raw = null): RdwApiResponse|static
    {
        $this->result = Rdw::getApi($this->getApi())
            ->setLicense($this->licenseplate)
            ->setEndpoints($this->endpoints)
            ->setTranslation($this->language)
            ->fetch();

        return (is_null($raw) || !$raw ? $this->get() : $this);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Order result
     *
     * @return RdwApiResponse
     */
    public function get(): RdwApiResponse
    {
        $result = new RdwApiResponse();

        $result->request = (object) array(
            'licenseplate' => $this->licenseplate,
            'endpoints' => $this->getEndpoints(),
            'language' => $this->language
        );
        // Translated response
        $result->response = $this->result['translated'] ?? [];
        // Raw response
        $result->raw = $this->result['raw'] ?? [];
        $result->status = $this->status($result);

        if (!is_null($this->outputformat)) {
            $result->request->outputformat = $this->outputformat;

            if (count($result->response) > 0) {
                // Translated Formated response
                $result->output = match ($this->outputformat) {
                    OutputFormat::XML => $result->toXml(true),
                    OutputFormat::JSON => $result->toJson(),
                    default => $result->toArray(),
                };
            }
        }

        return $result;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @param  string  $licenseplate
     * @return $this
     */
    public function setApi(string|int $api): static
    {
        if(!is_numeric($api)) {
            $r = array_flip(config('rdw-api.rdw_api_use_short'));
            if(isset($r[$api])) {
                $api = $r[$api];
            }
            else {
                $api = 0;
            }
        }

        $this->api = $api;
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @param  string  $licenseplate
     * @return $this
     */
    public function setLicenseplate(string $licenseplate): static
    {
        $this->licenseplate = $licenseplate;
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Force other language output than te selected language
     *
     * @param  string  $language
     * @return $this
     */
    public function setLanguage(string $language): static
    {
        $this->language = $language;
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * OutputFormat for result API request
     *
     * @param  OutputFormat|string  $type
     * @return $this
     */
    public function setOutputformat(OutputFormat|string $type): static
    {
        $this->outputformat = ($type instanceof OutputFormat ? $type : OutputFormat::getCase($type));
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Endpoints used for the request
     *
     * @param  array  $endpoints
     * @return $this
     */
    public function setEndpoints(array $endpoints = []): static
    {
        $this->endpoints = array_map(function ($endpoint) {
            return ($endpoint instanceof Endpoints ? $endpoint : Endpoints::getCase($endpoint));
        }, $endpoints);
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @param  array  $result
     * @return $this
     */
    public function setResult(array|string $result): static
    {
        $this->result = $result;
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @return int
     */
    public function getApi(): int
    {
        return $this->api;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @return string|null
     */
    public function getLicenseplate(): ?string
    {
        return $this->licenseplate;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language ?? app()->getLocale();
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getOutputformat(): string
    {
        return $this->outputformat ?? OutputFormat::ARRAY;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @return array|null
     */
    public function getEndpoints(): ?array
    {
        return $this->endpoints;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * @param  array  $result
     * @return $this
     */
    public function getResult(): array|string|null
    {
        return $this->result;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Is retrieved data valid
     *
     * @return $this
     */
    public function status(RdwApiResponse $request): bool
    {
        return match ($this->outputformat) {
            OutputFormat::JSON => json_validate($request->toJson()),
            OutputFormat::XML => (@simplexml_load_string($request->toXml()) ? 'true' : 'false'),
            default => is_array($request->toArray()) && count($request->toArray()) >= 1
        };
    }
}
