<?php

namespace kalanis\kw_cache\Files;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class Basic
 * @package kalanis\kw_cache\Files
 * Caching content by files
 */
class Basic implements ICache
{
    use TToString;

    protected CompositeAdapter $cacheStorage;
    /** @var string[] */
    protected array $cachePath = [];

    public function __construct(CompositeAdapter $cacheStorage)
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
            return $this->cacheStorage->exists($this->cachePath);
        } catch (FilesException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function set(string $content): bool
    {
        try {
            return $this->cacheStorage->saveFile($this->cachePath, $content);
        } catch (FilesException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function get(): string
    {
        try {
            return $this->exists()
                ? $this->toString(
                    Stuff::arrayToPath($this->cachePath),
                    $this->cacheStorage->readFile($this->cachePath)
                )
                : '';
        } catch (FilesException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function clear(): void
    {
        try {
            $this->cacheStorage->deleteFile($this->cachePath);
        } catch (FilesException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
