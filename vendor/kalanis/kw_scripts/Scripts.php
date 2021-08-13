<?php

namespace kalanis\kw_scripts;


use kalanis\kw_paths\Path;
use kalanis\kw_scripts\Interfaces\ILoader;


/**
 * Class Scripts
 * @package kalanis\kw_scripts
 * Store wanted scripts for rendering
 */
class Scripts
{
    /** @var ILoader */
    protected static $loader = null;
    /** @var string[][] */
    protected static $scripts = [];

    public static function init(Path $path, ?ILoader $loader = null): void
    {
        if (empty($loader) && empty(static::$loader)) {
            $loader = new Loaders\PhpLoader();
            $loader->setPathLib($path);
            static::$loader = $loader;
        }
    }

    public static function want(string $module, string $path): void
    {
        if (empty(static::$scripts[$module])) {
            static::$scripts[$module] = [];
        }
        static::$scripts[$module][] = $path;
    }

    public static function getAll(): array
    {
        return static::$scripts;
    }

    /**
     * @param string $module
     * @param string $path
     * @return string
     * @throws ScriptsException
     */
    public static function getFile(string $module, string $path): string
    {
        return static::$loader->load($module, $path);
    }
}
