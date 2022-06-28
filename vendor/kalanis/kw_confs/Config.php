<?php

namespace kalanis\kw_confs;


use kalanis\kw_confs\Interfaces\IConf;
use kalanis\kw_confs\Interfaces\ILoader;


/**
 * Class Config
 * @package kalanis\kw_confs
 * Store config data through system runtime
 */
class Config
{
    /** @var ILoader */
    protected static $loader = null;
    /** @var array<string, array<string|int, string|int|float|bool|null>> */
    protected static $configs = [];

    public static function init(ILoader $loader): void
    {
        static::$loader = $loader;
    }

    /**
     * @param string $module
     * @param string $conf
     * @throws ConfException
     */
    public static function load(string $module, string $conf = ''): void
    {
        $data = static::$loader->load($module, $conf);
        if (!is_null($data)) {
            static::loadData($module, $data);
        }
    }

    public static function loadClass(IConf $conf): void
    {
        static::loadData($conf->getConfName(), $conf->getSettings());
    }

    /**
     * @param string $module
     * @param array<string|int, string|int|float|bool|null> $confData
     */
    protected static function loadData(string $module, array $confData = []): void
    {
        if (empty(static::$configs[$module])) {
            static::$configs[$module] = [];
        }
        static::$configs[$module] = array_merge(static::$configs[$module], $confData);
    }

    /**
     * @param string $module
     * @param string $key
     * @param string|int|float|bool|null $defaultValue
     * @return string|int|float|bool|null
     */
    public static function get(string $module, string $key, $defaultValue = null)
    {
        return (isset(static::$configs[$module]) && isset(static::$configs[$module][$key]))
            ? static::$configs[$module][$key]
            : $defaultValue
        ;
    }

    /**
     * @param string $module
     * @param string $key
     * @param string|int|float|bool|null $defaultValue
     */
    public static function set(string $module, string $key, $defaultValue = null): void
    {
        isset(static::$configs[$module]) ? static::$configs[$module][$key] = $defaultValue : null ;
    }

    public static function getLoader(): ?ILoader
    {
        return static::$loader;
    }
}
