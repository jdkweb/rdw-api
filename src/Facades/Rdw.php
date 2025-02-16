<?php

namespace Jdkweb\RdwApi\Facades;

use Illuminate\Support\Facades\Facade;

class Rdw extends Facade
{
    /**
     * Get a task builder instance.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'RdwApi';
    }
}
