<?php

namespace kalanis\kw_files\Processing\Storage\Files;


use kalanis\kw_files\FilesException;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Basic
 * @package kalanis\kw_files\Processing\Storage\Files
 * Process files via lookup
 */
class Basic extends AFiles
{
    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
    }

    public function copyFile(string $source, string $dest): bool
    {
        try {
            return $this->storage->save($dest, $this->storage->load($source));
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function moveFile(string $source, string $dest): bool
    {
        $this->copyFile($source, $dest);
        $this->deleteFile($source);
        return true;
    }
}
