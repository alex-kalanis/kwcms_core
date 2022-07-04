<?php

namespace kalanis\kw_files\Processing\Storage\Dirs;


use kalanis\kw_files\FilesException;
use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class CanDir
 * @package kalanis\kw_files\Processing\Storage\Dirs
 * Process dirs via predefined api
 */
class CanDir extends ADirs
{
    /** @var IPassDirs|IStorage */
    protected $storage = null;

    public function __construct(IPassDirs $storage)
    {
        $this->storage = $storage;
    }

    public function createDir(string $entry, bool $deep = false): bool
    {
        try {
            return $this->storage->mkDir($entry, $deep);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function readDir(string $entry): array
    {
        try {
            return iterator_to_array($this->storage->lookup($entry));
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function copyDir(string $source, string $dest): bool
    {
        try {
            return $this->storage->copy($source, $dest);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function moveDir(string $source, string $dest): bool
    {
        try {
            return $this->storage->move($source, $dest);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function deleteDir(string $entry, bool $deep = false): bool
    {
        try {
            return $this->storage->rmDir($entry, $deep);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
