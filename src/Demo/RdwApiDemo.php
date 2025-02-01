<?php

namespace Jdkweb\Rdw\Demo;

use Illuminate\Support\Facades\View as SetView;
use Illuminate\View\View;
use Jdkweb\Rdw\Facades\Rdw;
use Jdkweb\Rdw\Enums\Endpoints;

class RdwApiDemo
{
    public function __construct()
    {
        $this->setLanguage();

        // Set view path
        SetView::addLocation(__DIR__ . '/views/');
    }

    /**
     * Set language with part of the uri
     *
     * @return void
     */
    protected function setLanguage():void
    {
        $language = app()->getLocale();
        if(preg_match("/^" . config('rdw-api.rdw_api_folder') . "\/". config('rdw-api.rdw_api_demo_slug') . "\/(nl|en)$/", request()->path())) {
            $language = str_replace(config('rdw-api.rdw_api_folder') ."/". config('rdw-api.rdw_api_demo_slug')."/","",request()->path());
        }
        app()->setLocale($language);
    }

    /**
     * Handle post form
     *
     * @return array|string|null
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function handleForm():array|string|null
    {
        $result = null;

        // Types (endpoints) selected
        if(count($this->getTypes()) < 1) return $result;

        // Is licenseplate set
        if(($licenseplate = request()->get('licenseplate'))) {

            $licenseplate = addslashes(trim(strip_tags($licenseplate)));

            // Base check
            if(!preg_match("/^[0-9A-Z\-]{6,8}$/i",$licenseplate)) {
                return $result;
            }

            // Output format
            $type = $this->getOutputFormat();

            // Call Api
            $result = Rdw::finder()
                ->setLicense($licenseplate)
                ->setEndpoints($this->getTypes())
                ->translate($this->getLanguage())
                ->format($type)
                ->fetch();

            // Change format
            if($type == "xml") {
                $result = $this->formatXML($result);
            }
        }

        return $result;
    }

    protected function formatXml(string $result): string
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($result);
        return htmlentities($dom->saveXML($dom->documentElement));
    }

    protected function getLanguage():string
    {
        return request()->get('language') ?? app()->getLocale();
    }

    protected function getTypes():array
    {
        return ($this->allTypes() ? Endpoints::names() : request()->get('settypes') ?? []);
    }

    protected function allTypes():bool
    {
        return request()->get('all') ?? 0;
    }

    protected function getOutputFormat():string
    {
        return request()->get('output') ?? 'array';
    }

    public function showForm(): View
    {
        return view('rwdapidemo',[
            'types' => Endpoints::cases(),
            'licenseplate' => request()->get('licenseplate'),
            'results'=> $this->handleForm(),
            'language' => $this->getLanguage(),
            'output' => $this->getOutputFormat(),
            'all' => $this->allTypes(),
            'settypes' => $this->getTypes()
        ]);
    }
}
