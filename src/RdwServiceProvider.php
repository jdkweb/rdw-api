<?php

namespace Jdkweb\RdwApi;

use Illuminate\Support\ServiceProvider;
use Jdkweb\RdwApi\Enums\OutputFormat;

class RdwServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     * Binding Rdw class into Laravel service container.
     *
     * @return void
     */
    final public function register():void
    {
        $this->app->singleton(Rdw::class, function ($app) {
            return new Rdw();
        });

        // rdw as alias: app('RdwApi')
        $this->app->alias(Rdw::class, 'RdwApi');
    }

    /**
     *
     * @return void
     */
    final public function boot():void
    {
        // php artisan vendor:publish --provider="Jdkweb\RdwApi\RdwServiceProvider" --tag="config"
        $this->publishes([
            dirname(__DIR__).'/config/rdw-api.php' => config_path('rdw-api.php'),
        ], 'config');

        // php artisan vendor:publish --provider="Jdkweb\RdwApi\RdwServiceProvider" --tag="lang"
        $this->publishes([
            dirname(__DIR__).'/lang' =>  lang_path('vendor/rdw-api'),
        ], 'lang');

        // Load lang
        $this->loadTranslationsFrom(dirname(__DIR__).'/lang/', 'rdw-api');

        // When not published Load config
        if (is_null(config('rdw-api.rdw_api_use'))) {
            $this->mergeConfigFrom(dirname(__DIR__).'/config/rdw-api.php', 'rdw-api');
        }

        // Demo route on and local
        if ((config('rdw-api.rdw_api_demo') || env('RDW_API_DEMO')) && env('APP_ENV') === 'local') {
            // Demo routes for normal form
            $this->loadRoutesFrom(dirname(__DIR__).'/routes/demo.php');
        }
    }
}
