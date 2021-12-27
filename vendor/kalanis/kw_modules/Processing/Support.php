<?php

namespace kalanis\kw_modules\Processing;


use kalanis\kw_paths;


/**
 * Class Support
 * @package kalanis\kw_modules\Processing
 * Basic class with supporting methods
 */
class Support
{
    public static function paramsIntoArray(string $param): array
    {
        parse_str($param, $result);
        return $result;
    }

    public static function paramsIntoString(array $param): string
    {
        return http_build_query($param);
    }

    public static function normalizeNamespacedName(string $moduleName): string
    {
        return implode('\\', // MUST be backslashes!! - translate to class name
            array_map('ucfirst',
                array_map(['\kalanis\kw_modules\Processing\Support', 'normalizeModuleName'],
                    array_filter(
                        array_filter(
                            kw_paths\Stuff::linkToArray($moduleName)
                        ), ['\kalanis\kw_paths\Stuff', 'notDots']
                    )
                )
            )
        );
    }

    public static function normalizeModuleName(string $moduleName): string
    {
        return implode('', array_map(['\kalanis\kw_modules\Processing\Support', 'moduleNamePart'], explode('-', $moduleName)));
    }

    public static function moduleNamePart(string $moduleName): string
    {
        return ucfirst(strtolower($moduleName));
    }

    public static function templateModuleName(string $moduleName): string
    {
        if (false !== preg_match_all('#([A-Z][a-z0-9]*)#u', $moduleName, $matches)) {
            return implode('_', array_map('strtoupper', $matches[1]));
        } else {
            return strtoupper($moduleName);
        }
    }

    public static function linkModuleName(string $moduleName): string
    {
        if (false !== preg_match_all('#([A-Z][a-z0-9]*)#u', $moduleName, $matches)) {
            return implode('-', array_map('strtolower', $matches[1]));
        } else {
            return strtoupper($moduleName);
        }
    }
}
