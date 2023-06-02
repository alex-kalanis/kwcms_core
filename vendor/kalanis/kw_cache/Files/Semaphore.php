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
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\SemaphoreException;


/**
 * Class Semaphore
 * @package kalanis\kw_cache\Files
 * Caching content by files - semaphore for detection
 */
class Semaphore implements ICache
{
    use TToString;

    /** @var CompositeAdapter */
    protected $lib = null;
    /** @var ISemaphore */
    protected $reloadSemaphore = null;
    /** @var string[] */
    protected $cachePath = [ICache::EXT_CACHE];

    public function __construct(CompositeAdapter $lib, ISemaphore $reloadSemaphore)
    {
        $this->lib = $lib;
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
            return $this->lib->exists($this->cachePath) && !$this->reloadSemaphore->has();
        } catch (SemaphoreException | FilesException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function set(string $content): bool
    {
        try {
            $result = $this->lib->saveFile($this->cachePath, $content);
            if (false === $result) {
                return false;
            }
            // remove signal to save
            if ($this->reloadSemaphore->has()) {
                $this->reloadSemaphore->remove();
            }
        } catch (SemaphoreException | FilesException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
        return true;
    }

    public function get(): string
    {
        try {
            return $this->exists()
                ? $this->toString(Stuff::arrayToPath($this->cachePath), $this->lib->readFile($this->cachePath))
                : ''
            ;
        } catch (FilesException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function clear(): void
    {
        try {
            $this->lib->deleteFile($this->cachePath);
        } catch (FilesException | PathsException $ex) {
            throw new CacheException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
