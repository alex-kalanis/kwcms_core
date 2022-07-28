<?php

namespace kalanis\kw_files\Processing\Storage\Nodes;


use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\Interfaces\IStorage;


/**
 * Class CanDir
 * @package kalanis\kw_files\Processing\Storage\Nodes
 * Process dirs via predefined api
 */
class CanDir extends ANodes
{
    /** @var IPassDirs|IStorage */
    protected $storage = null;

    public function __construct(IPassDirs $storage)
    {
        $this->storage = $storage;
    }

    public function exists(array $entry): bool
    {
        $path = $this->compactName($entry, $this->getStorageSeparator());
        return $this->storage->exists($path);
    }

    public function isDir(array $entry): bool
    {
        $path = $this->compactName($entry, $this->getStorageSeparator());
        return $this->storage->isDir($path);
    }

    public function isFile(array $entry): bool
    {
        $path = $this->compactName($entry, $this->getStorageSeparator());
        return $this->storage->isFile($path);
    }

    public function size(array $entry): ?int
    {
        $path = $this->compactName($entry, $this->getStorageSeparator());
        return $this->storage->size($path);
    }

    public function created(array $entry): ?int
    {
        $path = $this->compactName($entry, $this->getStorageSeparator());
        return $this->storage->created($path);
    }
}
