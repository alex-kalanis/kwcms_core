<?php

namespace kalanis\kw_cache\Storage;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
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
    protected IStorage $storage;
    protected ISemaphore $reloadSemaphore;
    /** @var string[] */
    protected array $cachePath = [ICache::EXT_CACHE];

    public function __construct(IStorage $cacheStorage, ISemaphore $reloadSemaphore)
    {
        $this->storage = $cacheStorage;
        $this->reloadSemaphore = $reloadSemaphore;
    }

    public function init(array $what): void
    {
        $arr = new ArrayPath();
        $arr->setArray($what);
        $this->cachePath = array_merge(
            $arr->getArrayDirectory(),
            [$arr->getFileName() . ICache::EXT_CACHE]
        );
    }

    public function exists(): bool
    {
        try {
            $cachePath = Stuff::arrayToPath($this->cachePath);
            return $this->storage->exists($cachePath) && !$this->reloadSemaphore->has();
        } catch (SemaphoreException | StorageException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function set(string $content): bool
    {
        try {
            $cachePath = Stuff::arrayToPath($this->cachePath);
            $result = $this->storage->write($cachePath, $content, null);
            if (false === $result) {
                return false;
            }
            // remove signal to save
            if ($this->reloadSemaphore->has()) {
                $this->reloadSemaphore->remove();
            }
        } catch (SemaphoreException | StorageException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
        return true;
    }

    public function get(): string
    {
        try {
            $cachePath = Stuff::arrayToPath($this->cachePath);
            return $this->exists() ? strval($this->storage->read($cachePath)) : '';
        } catch (StorageException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function clear(): void
    {
        try {
            $cachePath = Stuff::arrayToPath($this->cachePath);
            $this->storage->remove($cachePath);
        } catch (StorageException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
