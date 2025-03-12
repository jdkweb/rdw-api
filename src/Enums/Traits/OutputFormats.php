<?php

namespace Jdkweb\RdwApi\Enums\Traits;

use Jdkweb\RdwApi\Enums\Interface\OutputFormat;

trait OutputFormats
{
    /**
     * Select label
     *
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return match ($this) {
            self::ARRAY => __('rdw-api::enums.array'),
            self::JSON => __('rdw-api::enums.json'),
            self::XML => __('rdw-api::enums.xml'),
        };
    }

    public static function getCase(OutputFormat|string $type): OutputFormat
    {
        if ($type instanceof OutputFormat) {
            return $type;
        }

        $arr = array_filter(self::cases(), fn($enum) => (strtoupper($type) == $enum->name || $type == $enum->value));

        if (empty($arr)) {
            return self::ARRAY;
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
