<?php

namespace kalanis\kw_cache\Cache;


use kalanis\kw_cache\Interfaces\ICache;


/**
 * Class Dual
 * @package kalanis\kw_cache\Cache
 * Caching content in any cache - another cache as semaphore for detection
 */
class Dual implements ICache
{
    /** @var ICache|null */
    protected $storageCache = null;
    /** @var ICache|null */
    protected $reloadCache = null;

    public function __construct(ICache $storageCache, ?ICache $reloadCache = null)
    {
        $this->storageCache = $storageCache;
        $this->reloadCache = $reloadCache ?: $storageCache;
    }

    public function init(string $what): void
    {
        $this->storageCache->init($what);
        $this->reloadCache->init($what);
    }

    public function exists(): bool
    {
        return $this->storageCache->exists() && !$this->reloadCache->exists();
    }

    public function set(string $content): bool
    {
        $result = $this->storageCache->set($content);
        if (false === $result) {
            return false;
        }
        # remove signal to save
        if ($this->reloadCache->exists()) {
            $this->reloadCache->clear();
        }
        return true;
    }

    public function get(): string
    {
        return $this->exists() ? $this->storageCache->get() : '' ;
    }

    public function clear(): void
    {
        $this->storageCache->clear();
    }
}
