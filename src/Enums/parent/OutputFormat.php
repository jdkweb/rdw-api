<?php

namespace Jdkweb\RdwApi\Enums;

enum OutputFormat: string
{
    use OutputFormatTrait;

    case ARRAY = 'array';
    case JSON = 'json';
    case XML = 'xml';
}
