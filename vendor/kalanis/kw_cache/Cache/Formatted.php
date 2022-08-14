<?php

namespace kalanis\kw_cache\Cache;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces;


/**
 * Class Formatted
 * @package kalanis\kw_cache\Cache
 * Caching content in any cache - another cache as semaphore for detection
 */
class Formatted
{
    /** @var Interfaces\ICache */
    protected $cache = null;
    /** @var Interfaces\IFormat */
    protected $format = null;

    public function __construct(Interfaces\ICache $cache, Interfaces\IFormat $format)
    {
        $this->cache = $cache;
        $this->format = $format;
    }

    /**
     * @param string $what
     * @throws CacheException
     */
    public function init(string $what): void
    {
        $this->cache->init($what);
    }

    /**
     * @throws CacheException
     * @return bool
     */
    public function exists(): bool
    {
        return $this->cache->exists();
    }

    /**
     * @param mixed $content
     * @throws CacheException
     * @return bool
     */
    public function set($content): bool
    {
        return $this->cache->set(strval($this->format->pack($content)));
    }

    /**
     * @throws CacheException
     * @return mixed
     */
    public function get()
    {
        return $this->exists() ? $this->format->unpack($this->cache->get()) : null;
    }

    /**
     * @throws CacheException
     */
    public function clear(): void
    {
        $this->cache->clear();
    }
}
