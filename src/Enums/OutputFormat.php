<?php

/**
 * Hack for loading an enum using the getLabel method that works in both laravel and Filament
 *
 * If rdw-api is used without Filament the HasLabel implements will give an error.
 *
 * ! Composer dump-autoload wil not accept this file without class / namespace
 *
 * extends is not allowed in enums
 */
if (! (\Composer\InstalledVersions::isInstalled('filament/filament')) ) {
    require_once 'parent/OutputFormat.php';
}
elseif((\Composer\InstalledVersions::isInstalled('jdkweb/rdw-api-filament'))) {
    $path = app()->basePath('/vendor/jdkweb/rdw-api-filament/src/Enums') . '/OutputFormat.php';
    if(file_exists($path)) {
        require_once $path;
    }
}
