<?php

namespace Jdkweb\RdwApi\Enums;

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
