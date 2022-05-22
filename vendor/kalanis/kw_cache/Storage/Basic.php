<?php

namespace kalanis\kw_cache\Storage;


use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_storage\Storage\Storage;
use kalanis\kw_storage\StorageException;


/**
 * Class Basic
 * @package kalanis\kw_cache\Storage
 * Caching content in storage
 */
class Basic implements ICache
{
    /** @var Storage|null */
    protected $cacheStorage = null;
    protected $cachePath = '';

    public function __construct(Storage $cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
    }

    public function init(string $what): void
    {
        $this->cachePath = $what . ICache::EXT_CACHE;
    }

    public function exists(): bool
    {
        return $this->cacheStorage->exists($this->cachePath);
    }

    /**
     * @param string $content
     * @return bool
     * @throws StorageException
     */
    public function set(string $content): bool
    {
        return $this->cacheStorage->write($this->cachePath, $content, null);
    }

    /**
     * @return string
     * @throws StorageException
     */
    public function get(): string
    {
        return $this->exists() ? $this->cacheStorage->read($this->cachePath) : '';
    }

    /**
     * @throws StorageException
     */
    public function clear(): void
    {
        $this->cacheStorage->remove($this->cachePath);
    }
}
