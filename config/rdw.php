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

    'rdw_use' => 0,

    'rdw_use_short' => [
        'opendata',
        'overheid'
    ],

    'rdw_api' => [
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
    | Test URI
    |--------------------------------------------------------------------------
    |
    | make (in)active
    |
    | /test-rdw
    |
    */

    'rdw_test_uri' => 1,
];
