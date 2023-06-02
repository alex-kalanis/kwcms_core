<?php

namespace kalanis\kw_cache\Cache;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\SemaphoreException;


/**
 * Class Semaphore
 * @package kalanis\kw_cache\Cache
 * Caching content in any cache - semaphore for detection
 */
class Semaphore implements ICache
{
    /** @var ICache */
    protected $cache = null;
    /** @var ISemaphore */
    protected $reloadSemaphore = null;

    public function __construct(ICache $cache, ISemaphore $reloadSemaphore)
    {
        $this->cache = $cache;
        $this->reloadSemaphore = $reloadSemaphore;
    }

    public function init(array $what): void
    {
        $this->cache->init($what);
    }

    public function exists(): bool
    {
        try {
            return $this->cache->exists() && !$this->reloadSemaphore->has();
        } catch (SemaphoreException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function set(string $content): bool
    {
        $result = $this->cache->set($content);
        if (false === $result) {
            return false;
        }
        // remove signal to save
        try {
            if ($this->reloadSemaphore->has()) {
                $this->reloadSemaphore->remove();
            }
        } catch (SemaphoreException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
        return true;
    }

    public function get(): string
    {
        return $this->exists() ? $this->cache->get() : '' ;
    }

    public function clear(): void
    {
        $this->cache->clear();
    }
}
