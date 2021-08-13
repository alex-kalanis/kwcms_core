<?php

namespace kalanis\kw_extras;


use kalanis\kw_storage\Storage;


/**
 * Class Cache
 * @package kalanis\kw_extras
 * Caching content in storage
 */
class Cache implements Interfaces\ICache
{
    protected $content = null;
    protected $storage = '';
    protected $cachePath = '';
    protected $reloadPath = '';

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function init(string $what): self
    {
        $this->cachePath = $what . Interfaces\ICache::EXT_CACHE;
        $this->reloadPath = $what . Interfaces\ICache::EXT_RELOAD;
        return $this;
    }

    public function wantReload(): bool
    {
        return $this->storage->exists($this->reloadPath);
    }

    public function isAvailable(): bool
    {
        return $this->storage->exists($this->cachePath);
    }

    public function save(string $content): bool
    {
        $this->content = $content;
        $result = $this->storage->set($this->cachePath, $content, null);
        if (false === $result) {
            return false;
        }
        # remove signal to save
        if ($this->wantReload()) {
            $this->storage->delete($this->reloadPath);
        }
        return true;
    }

    public function get(): string
    {
        if ($this->isAvailable() && is_null($this->content)) {
            $this->content = $this->storage->get($this->cachePath);
        }
        return strval($this->content);
    }

    public function clear(): void
    {
        $this->content = null;
    }
}
