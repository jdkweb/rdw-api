<?php

namespace Jdkweb\RdwApi\Enums\Traits;

use Jdkweb\RdwApi\Enums\Interface\Endpoint;

trait Endpoints
{
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

    public static function getCase(string $type): ?Endpoint
    {
        $arr = array_filter(self::cases(), fn($enum) => (strtoupper($type) === $enum->name || $type === $enum->value));

        if (count($arr)==0) {
            return null;
        }

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
