<?php

namespace kalanis\kw_modules;


use kalanis\kw_paths;


/**
 * Class Support
 * @package kalanis\kw_modules
 * Basic class with supporting methods
 */
class Support
{
    /**
     * Get module from some preset path
     * @param string[] $modulePath
     * @return string[]
     */
    public static function modulePathFromDirPath(array $modulePath): array
    {
        return array_values(array_map('ucfirst',
            array_map([self::class, 'normalizeModuleName'],
                array_filter(
                    array_filter(
                        $modulePath
                    ), [kw_paths\Stuff::class, 'notDots']
                )
            )
        ));
    }

    /**
     * Get module from template format
     * {SOMETHING_NAMED__UNDER__PATH}
     * @param string $name
     * @return string[]
     */
    public static function modulePathFromTemplate(string $name): array
    {
        return array_values(array_map(
            'ucfirst',
            array_map(
                [self::class, 'normalizeTemplateModuleName'],
                array_map(
                    'strtolower',
                    explode('__', $name)
                )
            )
        ));
    }

    public static function clearModuleName(string $name): string
    {
        return strtr($name, ['/' => '', '!' => '']);
    }

    public static function normalizeModuleName(string $moduleName): string
    {
        return implode('', array_map([self::class, 'moduleNamePart'], explode('-', $moduleName)));
    }

    public static function normalizeTemplateModuleName(string $moduleName): string
    {
        return implode('', array_map([self::class, 'moduleNamePart'], explode('_', $moduleName)));
    }

    public static function moduleNamePart(string $moduleName): string
    {
        return ucfirst(strtolower($moduleName));
    }

    /**
     * @param string[] $path
     * @return string
     * Reverse for
     * @see \kalanis\kw_modules\Support::modulePathFromTemplate
     */
    public static function templatePathForModule(array $path): string
    {
        return strval(implode(
            '__',
            array_map([self::class, 'templateModuleName'], $path)
        ));
    }

    public static function templateModuleName(string $moduleName): string
    {
        if (!empty(preg_match_all('#([A-Z][a-z0-9]*)#u', $moduleName, $matches))) {
            return implode('_', array_map('mb_strtoupper', $matches[1]));
        } else {
            return mb_strtoupper($moduleName);
        }
    }
}
