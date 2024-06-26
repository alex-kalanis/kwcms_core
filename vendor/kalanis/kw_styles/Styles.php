<?php

namespace kalanis\kw_styles;


use kalanis\kw_styles\Interfaces\ILoader;


/**
 * Class Styles
 * @package kalanis\kw_styles
 * Store styles wanted for rendering
 */
class Styles
{
    protected static ?ILoader $loader = null;
    /** @var array<string, array<int, string>> */
    protected static array $styles = [];

    public static function init(?ILoader $loader): void
    {
        static::$loader = $loader;
    }

    public static function want(string $module, string $path): void
    {
        if (empty(static::$styles[$module])) {
            static::$styles[$module] = [];
        }
        static::$styles[$module][] = $path;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function getAll(): array
    {
        return static::$styles;
    }

    /**
     * @param string $module
     * @param string $path
     * @throws StylesException
     * @return string|null
     */
    public static function getFile(string $module, string $path): ?string
    {
        if (!static::$loader) {
            throw new StylesException('Set the loader first!');
        }
        return static::$loader->load($module, $path);
    }
}
