<?php

namespace kalanis\kw_routed_paths;


/**
 * Class Support
 * @package kalanis\kw_routed_paths
 * Basic class with supporting methods
 */
class Support
{
    public const PREFIX_MOD_NORMAL = 'm'; # show in normal mode
    public const PREFIX_MOD_SINGLE = 'ms'; # show module as single window
    public const PREFIX_USER = 'u'; # this is about that user
    public const PREFIX_LANG = 'l'; # this is about that language

    /**
     * @param string $name
     * @return string[]
     */
    public static function moduleNameFromRequest(string $name): array
    {
        return array_map(
            'ucfirst',
            array_map(
                [self::class, 'normalizeModuleName'],
                array_map(
                    'strtolower',
                    explode('--', $name)
                )
            )
        );
    }

    public static function normalizeModuleName(string $moduleName): string
    {
        return implode('', array_map([self::class, 'moduleNamePart'], explode('-', $moduleName)));
    }

    public static function moduleNamePart(string $moduleName): string
    {
        return ucfirst(strtolower($moduleName));
    }

    /**
     * @param string[] $path
     * @return string
     */
    public static function requestFromModuleName(array $path): string
    {
        return implode('--', array_filter(array_map(
            [self::class, 'linkModuleName'],
            $path
        )));
    }

    public static function linkModuleName(string $moduleName): string
    {
        if (false != preg_match_all('#([A-Z][a-z0-9]*)#u', $moduleName, $matches)) {
            return implode('-', array_map('mb_strtolower', $matches[1]));
        } else {
            return mb_strtolower($moduleName);
        }
    }

    public static function prefixWithSeparator(string $prefix): string
    {
        return $prefix . ':';
    }
}
