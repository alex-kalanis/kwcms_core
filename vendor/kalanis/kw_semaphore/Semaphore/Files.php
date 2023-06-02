<?php

namespace kalanis\kw_semaphore\Semaphore;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\SemaphoreException;


/**
 * Class Files
 * @package kalanis\kw_semaphore\Semaphore
 * Data source for semaphore is files
 */
class Files implements ISemaphore
{
    /** @var string[] */
    protected $rootPath = [];
    /** @var CompositeAdapter */
    protected $lib = null;

    /**
     * @param CompositeAdapter $lib
     * @param string[] $rootPath
     */
    public function __construct(CompositeAdapter $lib, array $rootPath)
    {
        $libArr = new ArrayPath();
        $libArr->setArray($rootPath);
        $this->rootPath = array_merge(
            $libArr->getArrayDirectory(),
            [$libArr->getFileName() . static::EXT_SEMAPHORE]
        );
        $this->lib = $lib;
    }

    public function want(): bool
    {
        try {
            return $this->lib->saveFile($this->rootPath, static::TEXT_SEMAPHORE);
        } catch (FilesException | PathsException $ex) {
            throw new SemaphoreException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function has(): bool
    {
        try {
            return $this->lib->exists($this->rootPath);
        } catch (FilesException | PathsException $ex) {
            throw new SemaphoreException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function remove(): bool
    {
        try {
            return $this->lib->deleteFile($this->rootPath);
        } catch (FilesException | PathsException $ex) {
            throw new SemaphoreException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
