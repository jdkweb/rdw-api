<?php

namespace Jdkweb\Rdw\Enums;

enum Endpoints: string
{
    // Select option values
    case VEHICLE = 'm9d7-ebf2.json';
    case VEHICLE_CLASS = 'kmfi-hrps.json';
    case FUEL = '8ys7-d773.json';
    case BODYWORK = 'vezc-m2t6.json';
    case BODYWORK_SPECIFIC = 'jhie-znh9.json';
    case AXLES = '3huj-srit.json';
    //case TRACKS = '3xwf-ince.json';
    //case FERRARI = 'pmhw-w82q.json';

    /**
     * Select label
     *
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return match ($this) {
            self::VEHICLE => __('rdw-api::enums.vehicle'),
            self::VEHICLE_CLASS => __('rdw-api::enums.vehicle_class'),
            self::FUEL => __('rdw-api::enums.fuel'),
            self::BODYWORK => __('rdw-api::enums.bodywork'),
            self::BODYWORK_SPECIFIC => __('rdw-api::enums.bodywork_specific'),
            self::AXLES => __('rdw-api::enums.axles'),
            //self::TRACKS => __('rdw-api::enums.tracks'),
            //self::FERRARI => __('rdw-api::enums.ferrari'),
        };
    }

    public function getName(): ?string
    {
        return match ($this) {
            self::VEHICLE => __('rdw-api::enums.VEHICLE'),
            self::VEHICLE_CLASS => __('rdw-api::enums.VEHICLE_CLASS'),
            self::FUEL => __('rdw-api::enums.FUEL'),
            self::BODYWORK => __('rdw-api::enums.BODYWORK'),
            self::BODYWORK_SPECIFIC => __('rdw-api::enums.BODYWORK_SPECIFIC'),
            self::AXLES => __('rdw-api::enums.AXLES'),
            //self::TRACKS => __('rdw-api::enums.TRACKS'),
            //self::FERRARI => __('rdw-api::enums.ferrari'),
        };
    }

    static public function getOptions(array $names = [], bool $shortname = false): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            if(empty($names) || in_array($case->name, $names)) {
                $options[$case->name] = (!$shortname ? $case->getLabel() : $case->getName());
            }
        }
        return $options;
    }

    public static function getCase(string $type): ?Endpoints
    {
        $arr = array_filter(self::cases(), fn($enum) => ($type == $enum->name));

        if(count($arr)==0) return null;

        return reset($arr);
    }

    public static function names(): array
    {
        return array_map(fn($enum) => $enum->name, self::cases());
    }

    public static function values(): array
    {
        return array_map(fn($enum) => $enum->value, self::cases());
    }
}
