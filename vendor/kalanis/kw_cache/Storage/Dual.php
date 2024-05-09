<?php

namespace kalanis\kw_cache\Storage;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Dual
 * @package kalanis\kw_cache\Storage
 * Caching content in storage - file as semaphore
 */
class Dual implements ICache
{
    protected IStorage $cacheStorage;
    protected IStorage $reloadStorage;
    /** @var string[] */
    protected array $cachePath = [ICache::EXT_CACHE];
    /** @var string[] */
    protected array $reloadPath = [ICache::EXT_RELOAD];

    public function __construct(IStorage $cacheStorage, ?IStorage $reloadStorage = null)
    {
        $this->cacheStorage = $cacheStorage;
        $this->reloadStorage = $reloadStorage ?: $cacheStorage;
    }

    public function init(array $what): void
    {
        $arr = new ArrayPath();
        $arr->setArray($what);
        $this->cachePath = array_merge(
            $arr->getArrayDirectory(),
            [$arr->getFileName() . ICache::EXT_CACHE]
        );
        $this->reloadPath = array_merge(
            $arr->getArrayDirectory(),
            [$arr->getFileName() . ICache::EXT_RELOAD]
        );
    }

    public function exists(): bool
    {
        try {
            $cachePath = Stuff::arrayToPath($this->cachePath);
            $reloadPath = Stuff::arrayToPath($this->reloadPath);
            return $this->cacheStorage->exists($cachePath) && !$this->reloadStorage->exists($reloadPath);
        } catch (StorageException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function set(string $content): bool
    {
        try {
            $cachePath = Stuff::arrayToPath($this->cachePath);
            $reloadPath = Stuff::arrayToPath($this->reloadPath);
            $result = $this->cacheStorage->write($cachePath, $content, null);
            if (false === $result) {
                return false;
            }
            // remove signal to save
            if ($this->reloadStorage->exists($reloadPath)) {
                $this->reloadStorage->remove($reloadPath);
            }
            return true;
        } catch (StorageException | PathsException$ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function get(): string
    {
        try {
            $cachePath = Stuff::arrayToPath($this->cachePath);
            return $this->exists() ? strval($this->cacheStorage->read($cachePath)) : '';
        } catch (StorageException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function clear(): void
    {
        try {
            $cachePath = Stuff::arrayToPath($this->cachePath);
            $this->cacheStorage->remove($cachePath);
        } catch (StorageException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
