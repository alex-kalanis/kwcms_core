<?php

namespace kalanis\kw_scripts;


use kalanis\kw_scripts\Interfaces\ILoader;


/**
 * Class Scripts
 * @package kalanis\kw_scripts
 * Store wanted scripts for rendering
 */
class Scripts
{
    protected static ?ILoader $loader = null;
    /** @var array<string, array<int, string>> */
    protected static array $scripts = [];

    public static function init(?ILoader $loader): void
    {
        static::$loader = $loader;
    }

    public static function want(string $module, string $path): void
    {
        if (empty(static::$scripts[$module])) {
            static::$scripts[$module] = [];
        }
        static::$scripts[$module][] = $path;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function getAll(): array
    {
        return static::$scripts;
    }

    /**
     * @param string $module
     * @param string $path
     * @throws ScriptsException
     * @return string|null
     */
    public static function getFile(string $module, string $path): ?string
    {
        if (is_null(static::$loader)) {
            throw new ScriptsException('Set the loader first!');
        }
        return static::$loader->load($module, $path);
    }
}
