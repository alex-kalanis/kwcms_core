<?php

namespace kalanis\kw_cache\Storage;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Dual
 * @package kalanis\kw_cache\Storage
 * Caching content in storage - file as semaphore
 */
class Dual implements ICache
{
    /** @var IStorage */
    protected $cacheStorage = null;
    /** @var IStorage */
    protected $reloadStorage = null;
    /** @var string */
    protected $cachePath = '';
    /** @var string */
    protected $reloadPath = '';

    public function __construct(IStorage $cacheStorage, ?IStorage $reloadStorage = null)
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
        try {
            return $this->cacheStorage->exists($this->cachePath) && !$this->reloadStorage->exists($this->reloadPath);
        } catch (StorageException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function set(string $content): bool
    {
        try {
            $result = $this->cacheStorage->write($this->cachePath, $content, null);
            if (false === $result) {
                return false;
            }
            // remove signal to save
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
