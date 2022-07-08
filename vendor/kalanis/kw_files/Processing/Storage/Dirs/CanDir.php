<?php

namespace kalanis\kw_files\Processing\Storage\Dirs;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
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

    public function createDir(array $entry, bool $deep = false): bool
    {
        try {
            return $this->storage->mkDir(
                $this->compactName($entry, $this->getStorageSeparator()),
                $deep
            );
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false): array
    {
        try {
            $files = [];
            $entryPath = $this->compactName($entry, $this->getStorageSeparator());
            foreach ($this->storage->lookup($entryPath) as $item) {
                $currentPath = $this->compactName($entry + [$item], $this->getStorageSeparator());
                $sub = new Node();
                if ($this->storage->isDir($currentPath)) {
                    $sub->setData(
                        $this->expandName($currentPath),
                        0,
                        ITypes::TYPE_DIR
                    );
                } else {
                    // normal node - file
                    $sub->setData(
                        $this->expandName($currentPath),
                        $wantSize ? $this->storage->size($currentPath) : 0,
                        ITypes::TYPE_FILE
                    );
                }
                $files[] = $sub;
            }
            return $files;
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function copyDir(array $source, array $dest): bool
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

    public function moveDir(array $source, array $dest): bool
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

    public function deleteDir(array $entry, bool $deep = false): bool
    {
        try {
            return $this->storage->rmDir(
                $this->compactName($entry, $this->getStorageSeparator()),
                $deep
            );
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
