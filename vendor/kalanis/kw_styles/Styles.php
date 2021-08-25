<?php

namespace kalanis\kw_styles;


use kalanis\kw_paths\Path;
use kalanis\kw_styles\Interfaces\ILoader;


/**
 * Class Styles
 * @package kalanis\kw_styles
 * Store styles wanted for rendering
 */
class Styles
{
    /** @var ILoader */
    protected static $loader = null;
    /** @var string[][] */
    protected static $styles = [];

    public static function init(Path $path, ?ILoader $loader = null): void
    {
        if (empty($loader) && empty(static::$loader)) {
            $loader = new Loaders\PhpLoader();
            $loader->setPathLib($path);
            static::$loader = $loader;
        } elseif ($loader) {
            static::$loader = $loader;
        }
    }

    public static function reset(Path $path, ?ILoader $loader = null): void
    {
        static::$loader = null;
        static::init($path, $loader);
    }

    public static function want(string $module, string $path): void
    {
        if (empty(static::$styles[$module])) {
            static::$styles[$module] = [];
        }
        static::$styles[$module][] = $path;
    }

    public static function getAll(): array
    {
        return static::$styles;
    }

    /**
     * @param string $module
     * @param string $path
     * @return string
     * @throws StylesException
     */
    public static function getFile(string $module, string $path): string
    {
        return static::$loader->load($module, $path);
    }
}
