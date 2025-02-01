<?php

namespace Jdkweb\Rdw\Enums;

enum OutputFormat: string
{
    case ARRAY = 'array';
    case JSON = 'json';
    case XML = 'xml';

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

    static public function getOptions(bool $shortname = false): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->name] = $case->getLabel();
        }
        return $options;
    }

    public static function getCase(string $type): OutputFormat
    {
        $arr = array_filter(self::cases(), fn($enum) => ($type == $enum->name));

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
