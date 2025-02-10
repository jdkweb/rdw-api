<?php

return [

    /*
    |--------------------------------------------------------------------------
    | USE API's for requests
    |--------------------------------------------------------------------------
    |
    | 0 => OpendataRwd https://opendata.rdw.nl/   (Free, DEFAULT)
    | 1 => OverheidIO  https://overheid.io/       (API key needed)
    */

    'rdw_api_use' => 0,

    'rdw_api_use_short' => [
        'opendata',
        'overheidio'
    ],

    'rdw_api_namespace' => [
        'OpendataRdw',
        'OverheidIO'
    ],

    /*
    |--------------------------------------------------------------------------
    | API key
    |--------------------------------------------------------------------------
    |
    | When needed (for overheid.io)
    */

    'rdw_api_key' => 'key',

    /*
    |--------------------------------------------------------------------------
    | SHOW DEMO
    |--------------------------------------------------------------------------
    |
    | make example (in)active for testing
    | set rdw_api_demo to 1
    | or add RDW_API_DEMO=1 to .env
    |
    |
    | /rdw-api/demo             base demo
    | /rdw-api/filament/demo    demo when filament extension is installed
    |
    */

    'rdw_api_demo' => 0,
    'rdw_api_folder' => 'rdw-api',
    'rdw_api_filament_folder' => 'filament',
    'rdw_api_demo_slug' => 'demo',

    /*
    |--------------------------------------------------------------------------
    | Filament config settings
    |--------------------------------------------------------------------------
    |
    | Model field names
    |
    */

    'license_plate' => 'licensePlate',
    'select_endpoints' => 'dataSet'
];
