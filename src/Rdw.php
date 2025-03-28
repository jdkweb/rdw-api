<?php

namespace Jdkweb\RdwApi;

use Jdkweb\RdwApi\Exceptions\RdwException;

class Rdw
{
    public function make()
    {
        return $this;
    }

    public function getApi(int|string $use_api = '')
    {
        // Get API class.
        $class = $this->getApiClass($this->selectApi($use_api));

        if (!class_exists($class)) {
            dd('error, class not exists: ' . $class);
        }

        return new $class();
    }

    public function __call(string $name, array $arguments)
    {
        throw new RdwException('Always start with the method Rdw::getApi()');
    }

    /**
     * Select API to use, Default = 0 (opendata)
     *
     * @param  int|string  $use_api
     * @return int
     */
    final protected function selectApi(int|string $use_api = ''):int
    {
        if (empty($use_api)) {
            return config('rdw-api.rdw_api_use');
        }

        if (is_string($use_api) && in_array($use_api, config('rdw-api.rdw_api_use_short'))) {
            return array_flip(config('rdw-api.rdw_api_use_short'))[$use_api];
        }

        if (is_int($use_api) && isset(config('rdw-api.rdw_api_use_short')[$use_api])) {
            return $use_api;
        }

        return 0;
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
