<?php

namespace Jdkweb\RdwApi\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Hack for loading an enum using the getLabel method that works in both laravel and Filament
 *
 * If rdw-api is used without Filament the HasLabel implements will give an error.
 *
 * ! Composer dump-autoload wil not accept this file without class / namespace
 *
 * extends is not allowed in enums
 */
if( (\Composer\InstalledVersions::isInstalled('filament/filament')) &&
    (\Composer\InstalledVersions::isInstalled('jdkweb/rdw-api-filament'))) {

    enum Endpoints: string implements HasLabel
    {
        use EndpointsTrait;

        // Select option values
        case VEHICLE = 'm9d7-ebf2.json';
        case VEHICLE_CLASS = 'kmfi-hrps.json';
        case FUEL = '8ys7-d773.json';
        case BODYWORK = 'vezc-m2t6.json';
        case BODYWORK_SPECIFIC = 'jhie-znh9.json';
        case AXLES = '3huj-srit.json';
        //case TRACKS = '3xwf-ince.json';
        //case FERRARI = 'pmhw-w82q.json';
    }
}
else {

    enum Endpoints: string
    {
        use EndpointsTrait;

        // Select option values
        case VEHICLE = 'm9d7-ebf2.json';
        case VEHICLE_CLASS = 'kmfi-hrps.json';
        case FUEL = '8ys7-d773.json';
        case BODYWORK = 'vezc-m2t6.json';
        case BODYWORK_SPECIFIC = 'jhie-znh9.json';
        case AXLES = '3huj-srit.json';
        //case TRACKS = '3xwf-ince.json';
        //case FERRARI = 'pmhw-w82q.json';
    }

}
