<?php

namespace Jdkweb\RdwApi\Demo;

use Illuminate\Support\Facades\View as SetView;
use Illuminate\View\View;
use Jdkweb\RdwApi\Enums\OutputFormat;
use Jdkweb\RdwApi\Enums\Endpoints;
use Jdkweb\RdwApi\Controllers\RdwApiRequest;

class RdwApiDemo
{

    public function __construct()
    {
        $this->setLanguage();

        // Set view path
        SetView::addLocation(__DIR__ . '/views/');
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Set language with part of the uri
     *
     * @return void
     */
    final protected function setLanguage():void
    {
        $language = app()->getLocale();
        if (preg_match(
            "/^" . config('rdw-api.rdw_api_folder') .
            "\/". config('rdw-api.rdw_api_demo_slug') .
            "\/(nl|en)$/",
            request()->path()
        )
        ) {
            $language = str_replace(
                config('rdw-api.rdw_api_folder') ."/".
                config('rdw-api.rdw_api_demo_slug')."/",
                "",
                request()->path()
            );
        }
        app()->setLocale($language);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Handle post form
     *
     * @return array|string|null
     */
    final protected function handleForm():array|string|null
    {
        $result = null;

        // Types (endpoints) selected
        if (count($this->getEndpoints()) < 1) {
            return $result;
        }

        // Is licenseplate set
        if (($licenseplate = request()->get('licenseplate'))) {
            $licenseplate = addslashes(trim(strip_tags($licenseplate)));

            // Base check
            if (!preg_match("/^[0-9A-Z\-]{6,8}$/i", $licenseplate)) {
                return $result;
            }

            // Call API Wrapper
            $result = RdwApiRequest::make()
                ->setLicenseplate($licenseplate)
                ->setEndpoints($this->getEndpoints())
                ->setLanguage($this->getLanguage())
                ->fetch();

            // Create output by format
            $result = match ($this->getOutputFormat()) {
                OutputFormat::XML->name => $result->toXml(true),
                OutputFormat::JSON->name => $result->toJson(),
                default => $result->toArray(),
            };
        }

        return $result;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Set language to force output result language
     *
     * @return string
     */
    final protected function getLanguage():string
    {
        return request()->get('language') ?? app()->getLocale();
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Selected Endpoints
     *
     * @return array
     */
    final protected function getEndpoints():array
    {
        if ($this->allEndpoints()) {
            return Endpoints::values();
        }

        $endpoints  = request()->get('endpoints') ?? [];

        // Check
        return array_filter(Endpoints::values(), function ($value) use ($endpoints) {
            return in_array($value, $endpoints);
        });
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Select all endpoints
     *
     * @return bool
     */
    final protected function allEndpoints():bool
    {
        return request()->get('allEndpoints') ?? 0;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Show this output format, array preset
     *
     * @return string
     */
    final protected function getOutputFormat():string
    {
        $output = request()->get('outputformat') ?? 'ARRAY';

        // Check
        return OutputFormat::getCase($output)?->name ?? 'ARRAY';
    }

    //------------------------------------------------------------------------------------------------------------------

    final public function showForm(): View
    {
        return view('rwdapidemo', [
            'allEndpoints' => (bool) $this->allEndpoints(),
            'endpoints' => (array) $this->getEndpoints(),
            'licenseplate' => (string) request()->get('licenseplate'),
            'language' => (string) $this->getLanguage(),
            'outputformat' => (string) $this->getOutputFormat(),
            'results'=> $this->handleForm(),
            'filamentInstalled' => (bool) !(\Composer\InstalledVersions::isInstalled('Jdkweb/rdw-api-filament'))
        ]);
    }
}
