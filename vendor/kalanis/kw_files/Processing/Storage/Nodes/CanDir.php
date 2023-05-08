<?php

namespace kalanis\kw_files\Processing\Storage\Nodes;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\StorageException;


/**
 * Class CanDir
 * @package kalanis\kw_files\Processing\Storage\Nodes
 * Process dirs via predefined api
 */
class CanDir extends ANodes
{
    /** @var IPassDirs */
    protected $storage = null;

    public function __construct(IPassDirs $storage, ?IFLTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->setLang($lang);
    }

    public function exists(array $entry): bool
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->exists($path);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function isReadable(array $entry): bool
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->isReadable($path);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function isWritable(array $entry): bool
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->isWritable($path);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function isDir(array $entry): bool
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->isDir($path);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function isFile(array $entry): bool
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->isFile($path);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function size(array $entry): ?int
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->size($path);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function created(array $entry): ?int
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->created($path);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @return string
     * @codeCoverageIgnore only when path fails
     */
    protected function noDirectoryDelimiterSet(): string
    {
        return $this->getLang()->flNoDirectoryDelimiterSet();
    }
}
