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
     * @return Formatted|ICache|null
     */
    public static function getCache()
    {
        return static::$cache;
    }

    /**
     * Init cache
     * @param string $what
     * @throws CacheException
     */
    public static function init(string $what): void
    {
        static::checkCache();
        static::$cache->/** @scrutinizer ignore-call */init($what);
    }

    /**
     * Is cache set?
     * @return boolean
     * @throws CacheException
     */
    public static function exists(): bool
    {
        static::checkCache();
        return static::$cache->/** @scrutinizer ignore-call */exists();
    }

    /**
     * Set data into cache
     * @param mixed $content
     * @throws CacheException
     * @return boolean
     */
    public static function set($content): bool
    {
        static::checkCache();
        return static::$cache->/** @scrutinizer ignore-call */set($content);
    }

    /**
     * Return cache content
     * @throws CacheException
     * @return mixed
     */
    public static function get()
    {
        static::checkCache();
        return static::$cache->/** @scrutinizer ignore-call */get();
    }

    /**
     * Delete data in cache
     * @throws CacheException
     */
    public static function clear(): void
    {
        static::checkCache();
        static::$cache->/** @scrutinizer ignore-call */clear();
    }

    /**
     * @throws CacheException
     */
    protected static function checkCache(): void
    {
        if (empty(static::$cache)) {
            throw new CacheException('Cache not initialized');
        }
    }
}
