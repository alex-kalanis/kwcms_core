<?php

namespace kalanis\kw_semaphore\Semaphore;


use kalanis\kw_paths\Stuff;
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Storage
 * @package kalanis\kw_semaphore\Semaphore
 * Data source for semaphore is storage
 */
class Storage implements ISemaphore
{
    /** @var string */
    protected $rootPath = '';
    /** @var IStorage */
    protected $storage = null;

    public function __construct(IStorage $storage, string $rootPath)
    {
        $this->rootPath = Stuff::removeEndingSlash($rootPath) . static::EXT_SEMAPHORE;
        $this->storage = $storage;
    }

    public function want(): bool
    {
        try {
            return $this->storage->write($this->rootPath, static::TEXT_SEMAPHORE);
        } catch (StorageException $ex) {
            throw new SemaphoreException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function has(): bool
    {
        try {
            return $this->storage->exists($this->rootPath);
        } catch (StorageException $ex) {
            throw new SemaphoreException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function remove(): bool
    {
        try {
            return $this->storage->remove($this->rootPath);
        } catch (StorageException $ex) {
            throw new SemaphoreException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
