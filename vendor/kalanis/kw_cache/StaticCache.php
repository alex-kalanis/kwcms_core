<?php

namespace kalanis\kw_cache;


use kalanis\kw_cache\Cache\Formatted;
use kalanis\kw_cache\Interfaces\ICache;


/**
 * Class StaticCache
 * @package kalanis\kw_cache
 * Static face for caching values in selected storage
 * Cannot implement interface due passing mixed content there and back
 */
class StaticCache
{
    /** @var ICache|Formatted|null */
    protected static $cache = null;

    /**
     * @param Formatted|ICache|null $cache
     */
    public static function setCache($cache = null): void
    {
        static::$cache = $cache;
    }

    /**
     * @throws CacheException
     * @return Formatted|ICache
     */
    public static function getCache()
    {
        if (empty(static::$cache)) {
            throw new CacheException('Cache not initialized');
        }
        return static::$cache;
    }

    /**
     * Init cache
     * @param string[] $what
     * @throws CacheException
     */
    public static function init(array $what): void
    {
        static::getCache()->init($what);
    }

    /**
     * Is cache set?
     * @throws CacheException
     * @return boolean
     */
    public static function exists(): bool
    {
        return static::getCache()->exists();
    }

    /**
     * Set data into cache
     * @param mixed $content
     * @throws CacheException
     * @return boolean
     */
    public static function set($content): bool
    {
        return static::getCache()->set($content);
    }

    /**
     * Return cache content
     * @throws CacheException
     * @return mixed
     */
    public static function get()
    {
        return static::getCache()->get();
    }

    /**
     * Delete data in cache
     * @throws CacheException
     */
    public static function clear(): void
    {
        static::getCache()->clear();
    }
}
