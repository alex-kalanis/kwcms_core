<?php

namespace kalanis\kw_files\Processing\Storage;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Processing\TNameFinder;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class ProcessFile
 * @package kalanis\kw_files\Processing\Storage
 * Process files in many ways
 */
class ProcessFile implements IProcessFiles
{
    use TNameFinder;

    /** @var IStorage */
    protected $storage = null;

    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
    }

    public function saveFile(string $targetName, $content): bool
    {
        try {
            return $this->storage->save($targetName, $content);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    protected function getSeparator(): string
    {
        return static::FREE_NAME_SEPARATOR;
    }

    protected function targetExists(string $path): bool
    {
        return $this->storage->exists($path);
    }

    public function readFile(string $entry, ?int $offset = null, ?int $length = null): string
    {
        try {
            $content = $this->storage->load($entry);
            return $content;
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
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

    public function deleteFile(string $entry): bool
    {
        try {
            return $this->storage->remove($entry);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
