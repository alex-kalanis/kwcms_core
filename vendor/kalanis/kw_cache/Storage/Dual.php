<?php

namespace kalanis\kw_cache\Storage;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_storage\Storage\Storage;
use kalanis\kw_storage\StorageException;


/**
 * Class Dual
 * @package kalanis\kw_cache\Storage
 * Caching content in storage - file as semaphore
 */
class Dual implements ICache
{
    /** @var Storage */
    protected $cacheStorage = null;
    /** @var Storage */
    protected $reloadStorage = null;
    /** @var string */
    protected $cachePath = '';
    /** @var string */
    protected $reloadPath = '';

    public function __construct(Storage $cacheStorage, ?Storage $reloadStorage = null)
    {
        $this->cacheStorage = $cacheStorage;
        $this->reloadStorage = $reloadStorage ?: $cacheStorage;
    }

    public function init(string $what): void
    {
        $this->cachePath = $what . ICache::EXT_CACHE;
        $this->reloadPath = $what . ICache::EXT_RELOAD;
    }

    public function exists(): bool
    {
        return $this->cacheStorage->exists($this->cachePath) && !$this->reloadStorage->exists($this->reloadPath);
    }

    public function set(string $content): bool
    {
        try {
            $result = $this->cacheStorage->write($this->cachePath, $content, null);
            if (false === $result) {
                return false;
            }
            # remove signal to save
            if ($this->reloadStorage->exists($this->reloadPath)) {
                $this->reloadStorage->remove($this->reloadPath);
            }
            return true;
        } catch (StorageException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function get(): string
    {
        try {
            return $this->exists() ? strval($this->cacheStorage->read($this->cachePath)) : '';
        } catch (StorageException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function clear(): void
    {
        try {
            $this->cacheStorage->remove($this->cachePath);
        } catch (StorageException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
