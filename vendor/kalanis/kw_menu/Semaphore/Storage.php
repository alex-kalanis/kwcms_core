<?php

namespace kalanis\kw_menu\Semaphore;


use kalanis\kw_menu\Interfaces\ISemaphore;
use kalanis\kw_menu\MenuException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_storage\Storage as XStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Storage
 * @package kalanis\kw_menu\Semaphore
 * Data source for semaphore is storage
 */
class Storage implements ISemaphore
{
    /** @var string */
    protected $rootPath = '';
    /** @var XStorage */
    protected $storage = null;

    public function __construct(XStorage $storage, string $rootPath)
    {
        $this->rootPath = Stuff::removeEndingSlash($rootPath) . static::EXT_SEMAPHORE;
        $this->storage = $storage;
    }

    public function want(): bool
    {
        try {
            return $this->storage->set($this->rootPath, 'RELOAD');
        } catch (StorageException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function has(): bool
    {
        try {
            return $this->storage->exists($this->rootPath);
        } catch (StorageException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function remove(): bool
    {
        try {
            return $this->storage->delete($this->rootPath);
        } catch (StorageException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
