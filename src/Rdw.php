<?php

namespace Jdkweb\Rdw;


class Rdw
{
    /**
     * Call API to get RDW info
     *
     * @return Api\Rdw
     */
    final public function finder(): Api\Rdw
    {
        $class = $this->getApiClass($this->selectApi(config('rdw-api.rdw_api_use')));

        if(!class_exists($class)) {
            dd('error, class not exists: ' . $class);
        }

        return new $class();
    }

    /**
     * Select API to use, Default = 0 (opendata)
     *
     * @param  int|string  $use_api
     * @return int
     */
    final protected function selectApi(int|string $use_api = ''):int
    {
        if(empty($use_api)) $use_api = config('rdw-api.rdw_api_use');

        //$key = (!is_numeric($use_api) ? 'rdw_api_use_short' : 'rdw_api_namespace');
        //dd($use_api, config('rdw-api.' . $key));
        //return config('rdw-api.' . $key)[$use_api] ?? 0;
        return $use_api;
    }

    /**
     * Api namespace for class
     *
     * @return string
     */
    final protected function getApiClass(int $use_api):string
    {
        return __NAMESPACE__ . "\\Api\\" . config('rdw-api.rdw_api_namespace')[$use_api];
    }
}
