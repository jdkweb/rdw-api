<?php

enum OutputFormat: string
{
    use OutputFormatTrait;

    case ARRAY = 'array';
    case JSON = 'json';
    case XML = 'xml';
}
