<?php

namespace kalanis\kw_cache;


use kalanis\kw_storage\Storage;
use kalanis\kw_storage\StorageException;


/**
 * Class Cache
 * @package kalanis\kw_extras
 * Caching content in storage
 */
class Cache implements Interfaces\ICache
{
    protected $content = null;
    /** @var Storage|null */
    protected $cacheStorage = null;
    /** @var Storage|null */
    protected $reloadStorage = null;
    protected $cachePath = '';
    protected $reloadPath = '';

    public function __construct(Storage $cacheStorage, ?Storage $reloadStorage = null)
    {
        $this->cacheStorage = $cacheStorage;
        $this->reloadStorage = $reloadStorage ?: $cacheStorage;
    }

    public function init(string $what): self
    {
        $this->cachePath = $what . Interfaces\ICache::EXT_CACHE;
        $this->reloadPath = $what . Interfaces\ICache::EXT_RELOAD;
        return $this;
    }

    /**
     * @return bool
     * @throws StorageException
     */
    public function wantReload(): bool
    {
        return $this->reloadStorage->exists($this->reloadPath);
    }

    /**
     * @return bool
     * @throws StorageException
     */
    public function isAvailable(): bool
    {
        return $this->cacheStorage->exists($this->cachePath);
    }

    /**
     * @param string $content
     * @return bool
     * @throws StorageException
     */
    public function save(string $content): bool
    {
        $this->content = $content;
        $result = $this->cacheStorage->set($this->cachePath, $content, null);
        if (false === $result) {
            return false;
        }
        # remove signal to save
        if ($this->wantReload()) {
            $this->reloadStorage->delete($this->reloadPath);
        }
        return true;
    }

    /**
     * @return string
     * @throws StorageException
     */
    public function get(): string
    {
        if ($this->isAvailable() && is_null($this->content)) {
            $this->content = $this->cacheStorage->get($this->cachePath);
        }
        return strval($this->content);
    }

    /**
     * @throws StorageException
     */
    public function reload(): void
    {
        $this->reloadStorage->set($this->reloadPath, 'CACHE RELOAD');
        $this->clear();
    }

    public function clear(): void
    {
        $this->content = null;
    }
}
