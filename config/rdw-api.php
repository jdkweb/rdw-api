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
        'overheid'
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

    'rdw_api_key' => '57b0cfe40746f2ddab489af7fd1fd9ba2cc0618663ad73fc14901687df94337b',

    /*
    |--------------------------------------------------------------------------
    | SHOW DEMO
    |--------------------------------------------------------------------------
    |
    | make example (in)active for testing
    |
    | /rdw-api-demo
    |
    */

    'rdw_api_demo' => 1,
    'rdw_api_demo_url' => 'rdw-api-demo',

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
