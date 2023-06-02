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
 * Class Dual
 * @package kalanis\kw_cache\Files
 * Caching content by files - file as semaphore
 */
class Dual implements ICache
{
    use TToString;

    /** @var CompositeAdapter */
    protected $cacheLib = null;
    /** @var CompositeAdapter */
    protected $reloadLib = null;
    /** @var string[] */
    protected $cachePath = [];
    /** @var string[] */
    protected $reloadPath = [];

    public function __construct(CompositeAdapter $cacheLib, ?CompositeAdapter $reloadLib = null)
    {
        $this->cacheLib = $cacheLib;
        $this->reloadLib = $reloadLib ?? $cacheLib;
    }

    public function init(array $what): void
    {
        $arr = new ArrayPath();
        $arr->setArray($what);
        $this->cachePath = array_merge(
            $arr->getArrayDirectory(),
            [$arr->getFileName() . ICache::EXT_CACHE]
        );;
        $this->reloadPath = array_merge(
            $arr->getArrayDirectory(),
            [$arr->getFileName() . ICache::EXT_RELOAD]
        );
    }

    public function exists(): bool
    {
        try {
            return $this->cacheLib->exists($this->cachePath) && !$this->reloadLib->exists($this->reloadPath);
        } catch (FilesException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function set(string $content): bool
    {
        try {
            $result = $this->cacheLib->saveFile($this->cachePath, $content);
            if (false === $result) {
                return false;
            }
            // remove signal to save
            if ($this->reloadLib->exists($this->reloadPath)) {
                $this->reloadLib->deleteFile($this->reloadPath);
            }
            return true;
        } catch (FilesException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function get(): string
    {
        try {
            return $this->exists()
                ? $this->toString(Stuff::arrayToPath($this->cachePath), $this->cacheLib->readFile($this->cachePath))
                : ''
            ;
        } catch (FilesException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function clear(): void
    {
        try {
            $this->cacheLib->deleteFile($this->cachePath);
        } catch (FilesException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
