<?php

namespace kalanis\kw_cache\Storage;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Basic
 * @package kalanis\kw_cache\Storage
 * Caching content in storage
 */
class Basic implements ICache
{
    /** @var IStorage */
    protected $cacheStorage = null;
    /** @var string */
    protected $cachePath = '';

    public function __construct(IStorage $cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
    }

    public function init(string $what): void
    {
        $this->cachePath = $what . ICache::EXT_CACHE;
    }

    public function exists(): bool
    {
        try {
            return $this->cacheStorage->exists($this->cachePath);
        } catch (StorageException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function set(string $content): bool
    {
        try {
            return $this->cacheStorage->write($this->cachePath, $content, null);
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
