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

    enum OutputFormat: string implements HasLabel
    {
        use OutputFormatTrait;

        case ARRAY = 'array';
        case JSON = 'json';
        case XML = 'xml';
    }
}
else {

    enum OutputFormat: string
    {
        use OutputFormatTrait;

        case ARRAY = 'array';
        case JSON = 'json';
        case XML = 'xml';
    }
}
