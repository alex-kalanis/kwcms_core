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
 * Class Basic
 * @package kalanis\kw_cache\Storage
 * Caching content in storage
 */
class Basic implements ICache
{
    /** @var IStorage */
    protected $cacheStorage = null;
    /** @var string[] */
    protected $cachePath = [ICache::EXT_CACHE];

    public function __construct(IStorage $cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
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
            return $this->cacheStorage->exists($cachePath);
        } catch (StorageException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function set(string $content): bool
    {
        try {
            $cachePath = Stuff::arrayToPath($this->cachePath);
            return $this->cacheStorage->write($cachePath, $content);
        } catch (StorageException | PathsException $ex) {
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
