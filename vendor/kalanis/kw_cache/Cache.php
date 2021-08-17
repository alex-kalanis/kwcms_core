<?php

namespace kalanis\kw_cache;


use kalanis\kw_storage\Storage;


/**
 * Class Cache
 * @package kalanis\kw_extras
 * Caching content in storage
 */
class Cache implements Interfaces\ICache
{
    protected $content = null;
    protected $cacheStorage = null;
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

    public function wantReload(): bool
    {
        return $this->reloadStorage->exists($this->reloadPath);
    }

    public function isAvailable(): bool
    {
        return $this->cacheStorage->exists($this->cachePath);
    }

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

    public function get(): string
    {
        if ($this->isAvailable() && is_null($this->content)) {
            $this->content = $this->cacheStorage->get($this->cachePath);
        }
        return strval($this->content);
    }

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
