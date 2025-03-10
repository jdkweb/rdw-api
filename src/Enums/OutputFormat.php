<?php
namespace Jdkweb\RdwApi\Enums;

use Filament\Support\Contracts\HasLabel;

enum OutputFormat: string
{
    use OutputFormatTrait;

    case ARRAY = 'array';
    case JSON = 'json';
    case XML = 'xml';
}

