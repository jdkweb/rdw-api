<?php

namespace Jdkweb\Rdw\Controllers;

use Filament\Forms\Form;
use Jdkweb\Rdw\Enums\Endpoints;
use Jdkweb\Rdw\Enums\OutputFormat;
use Jdkweb\Rdw\Exceptions\RdwException;
use Jdkweb\Rdw\Filament\Forms\Components\RdwApiLicenseplate;
use Spatie\ArrayToXml\ArrayToXml;

class RdwApiRequest
{
    /**
     * Callable setters and getters
     * @var array|string[]
     */
    private array $properties = ['licenseplate', 'language', 'outputformat', 'endpoints', 'result'];

    /**
     * Values for API request
     * @var string|null
     */
    private ?string $licenseplate = null;
    private ?array $endpoints = null;
    private ?string $language = null;
    private OutputFormat|null $outputformat = null;

    /**
     * Result API request
     * @var array|string|null
     */
    private array|string|null $result = null;

    /**
     * @var RdwApiRequest|null
     */
    private static ?RdwApiRequest $instance = null;

    public static function make(): static
    {
        // Singleton
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Call to Rdw API
     *
     * @return array|string
     */
    final public function rdwApiRequest(): static
    {
        $this->result = \Jdkweb\Rdw\Facades\Rdw::finder()
            ->setLicense($this->licenseplate)
            ->setEndpoints($this->endpoints)
            ->forceTranslation($this->language)
            ->fetch();

        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Order result
     *
     * @return RdwApiResponse
     */
    final public function get(): RdwApiResponse
    {
        $result = new RdwApiResponse();

        $result->request = (object) array(
            'licenseplate' => $this->licenseplate,
            'endpoints' => $this->getEndpoints(),
            'language' => $this->language,
            'outputformat' => $this->outputformat,
        );
        $result->response = $this->result ?? [];
        $result->status = $this->status($result);
        $result->output = '';

        if(!is_null($this->outputformat) && count($result->response) > 0) {
            $result->output = match ($this->outputformat) {
                OutputFormat::XML => $result->toXml(true),
                OutputFormat::JSON => $result->toJson(),
                Default => $result->toArray(),
            };
        }

        return $result;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Getters / Setters for properties
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (!preg_match('/^(set|get)[A-Z]{1}(\w+)$/', $name)) {
            return $this;
        }

        $action = substr(strtolower($name), 0,3);
        $name = substr(strtolower($name), 3);

        // getter
        if($action === 'get') return $this->{strtolower($name)};

        // setter
        if (in_array($name, $this->properties)) {
            $this->{strtolower($name)} = reset($arguments);
        }
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * OutputFormat for result API request
     *
     * @param  OutputFormat|string  $type
     * @return $this
     */
    final public function setOutputformat(OutputFormat|string $type): static
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
    final public function setEndpoints(array $endpoints = []): static
    {
        $this->endpoints = array_map(function ($endpoint) {
            return ($endpoint instanceof Endpoints ? $endpoint : Endpoints::getCase($endpoint));
        }, $endpoints);
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Get settings from the filament form
     *
     * @param  Form  $form
     * @return $this
     * @throws RdwException
     */
    final public function setFormData(Form $form): static
    {
        $data = $form->getState();
        $rdwApiLicenseplate = $this->getComponent($form);
        $licensePlateName = $rdwApiLicenseplate->getStatePath(false);

        // Set data for RDW request
        $this->licenseplate = $data[$licensePlateName];
        $this->outputformat = $rdwApiLicenseplate->getOutputFormat();
        $this->language = $rdwApiLicenseplate->getLanguage();

        return $this->setEndpoints($rdwApiLicenseplate->getDataset());
    }

    //------------------------------------------------------------------------------------------------------------------

    final protected function getComponent(Form $form): RdwApiLicenseplate
    {
        $components = $form->getFlatComponents();

        $licenseplate = null;

        foreach ($components as $component) {
            if ($component instanceof RdwApiLicenseplate) {
                $licenseplate =& $component;
                break;
            }
        }

        if (is_null($licenseplate)) {
            throw new RdwException(__('rdw-api::errors.component_error', [
                'class' => self::class,
                'component' => RdwApiLicenseplate::class
            ]));
        }

        return $licenseplate;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Is retrieved data valid
     *
     * @return $this
     */
    final public function status(RdwApiResponse $request): bool
    {
        return match ($this->outputformat) {
            OutputFormat::JSON => json_validate($request->toJson()),
            OutputFormat::XML => (@simplexml_load_string($request->toXml()) ? 'true' : 'false'),
            default => is_array($request->toArray()) && count($request->toArray()) >= 1
        };
    }
}
