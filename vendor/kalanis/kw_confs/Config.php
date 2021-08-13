<?php

namespace kalanis\kw_confs;


use kalanis\kw_confs\Interfaces\IConf;
use kalanis\kw_confs\Interfaces\ILoader;
use kalanis\kw_paths\Path;


/**
 * Class Config
 * @package kalanis\kw_confs
 * Store config data through system runtime
 */
class Config
{
    /** @var ILoader */
    protected static $loader = null;
    /** @var null|Path */
    protected static $paths = null;
    /** @var string[][] */
    protected static $configs = [];

    public static function init(Path $path, ?ILoader $loader = null): void
    {
        static::$paths = $path;
        if (empty($loader) && empty(static::$loader)) {
            $loader = new Loaders\PhpLoader();
            $loader->setPathLib($path);
        }
        static::$loader = $loader;
    }

    public static function getPath(): Path
    {
        return clone static::$paths;
    }

    public static function getOriginalPath(): Path
    {
        return static::$paths;
    }

    public static function load(string $module, string $conf = ''): void
    {
        static::loadData($module, static::$loader->load($module, $conf));
    }

    public static function loadClass(IConf $conf): void
    {
        static::loadData($conf->getConfName(), $conf->getSettings());
    }

    protected static function loadData(string $module, array $confData = []): void
    {
        if (empty(static::$configs[$module])) {
            static::$configs[$module] = [];
        }
        static::$configs[$module] = array_merge(static::$configs[$module], $confData);
    }

    public static function get(string $module, string $key, $defaultValue = null)
    {
        return (static::$configs[$module] && isset(static::$configs[$module][$key])) ? static::$configs[$module][$key] : $defaultValue ;
    }

    public static function getLoader(): ?ILoader
    {
        return static::$loader;
    }
}
