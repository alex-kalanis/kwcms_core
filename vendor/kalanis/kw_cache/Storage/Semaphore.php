<?php

namespace kalanis\kw_cache\Storage;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Semaphore
 * @package kalanis\kw_cache\Storage
 * Caching content in storage - semaphore for detection
 */
class Semaphore implements ICache
{
    /** @var IStorage */
    protected $storage = null;
    /** @var ISemaphore */
    protected $reloadSemaphore = null;
    /** @var string */
    protected $cachePath = '';

    public function __construct(IStorage $cacheStorage, ISemaphore $reloadSemaphore)
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
        } catch (SemaphoreException | StorageException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function set(string $content): bool
    {
        try {
            $result = $this->storage->write($this->cachePath, $content, null);
            if (false === $result) {
                return false;
            }
            # remove signal to save
            if ($this->reloadSemaphore->has()) {
                $this->reloadSemaphore->remove();
            }
        } catch (SemaphoreException | StorageException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
        return true;
    }

    public function get(): string
    {
        try {
            return $this->exists() ? strval($this->storage->read($this->cachePath)) : '';
        } catch (StorageException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function clear(): void
    {
        try {
            $this->storage->remove($this->cachePath);
        } catch (StorageException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
