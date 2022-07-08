<?php

namespace kalanis\kw_files\Processing\Storage\Files;


use kalanis\kw_files\FilesException;
use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class CanDir
 * @package kalanis\kw_files\Processing\Storage\Files
 * Process files via predefined api
 */
class CanDir extends AFiles
{
    /** @var IStorage|IPassDirs */
    protected $storage = null;

    public function __construct(IPassDirs $storage)
    {
        $this->storage = $storage;
    }

    public function copyFile(array $source, array $dest): bool
    {
        try {
            return $this->storage->copy(
                $this->compactName($source, $this->getStorageSeparator()),
                $this->compactName($dest, $this->getStorageSeparator())
            );
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function moveFile(array $source, array $dest): bool
    {
        try {
            return $this->storage->move(
                $this->compactName($source, $this->getStorageSeparator()),
                $this->compactName($dest, $this->getStorageSeparator())
            );
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
