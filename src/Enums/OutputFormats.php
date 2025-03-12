<?php
namespace Jdkweb\RdwApi\Enums;

enum OutputFormats: string implements Interface\OutputFormat
{
    use Traits\OutputFormats;

    case ARRAY = 'array';
    case JSON = 'json';
    case XML = 'xml';
}

