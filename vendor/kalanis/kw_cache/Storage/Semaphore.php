<?php

namespace kalanis\kw_cache\Storage;


use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_storage\Storage\Storage;
use kalanis\kw_storage\StorageException;


/**
 * Class Semaphore
 * @package kalanis\kw_cache\Storage
 * Caching content in storage - semaphore for detection
 */
class Semaphore implements ICache
{
    /** @var Storage */
    protected $storage = null;
    /** @var ISemaphore */
    protected $reloadSemaphore = null;
    /** @var string */
    protected $cachePath = '';

    public function __construct(Storage $cacheStorage, ISemaphore $reloadSemaphore)
    {
        $this->storage = $cacheStorage;
        $this->reloadSemaphore = $reloadSemaphore;
    }

    public function init(string $what): void
    {
        $this->cachePath = $what . ICache::EXT_CACHE;
    }

    public function exists(): bool
    {
        try {
            return $this->storage->exists($this->cachePath) && !$this->reloadSemaphore->has();
        } catch (SemaphoreException $ex) {
            throw new StorageException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function set(string $content): bool
    {
        $result = $this->storage->write($this->cachePath, $content, null);
        if (false === $result) {
            return false;
        }
        # remove signal to save
        try {
            if ($this->reloadSemaphore->has()) {
                $this->reloadSemaphore->remove();
            }
        } catch (SemaphoreException $ex) {
            throw new StorageException($ex->getMessage(), $ex->getCode(), $ex);
        }
        return true;
    }

    public function get(): string
    {
        return $this->exists() ? strval($this->storage->read($this->cachePath)) : '' ;
    }

    public function clear(): void
    {
        $this->storage->remove($this->cachePath);
    }
}
