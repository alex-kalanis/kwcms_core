<?php

namespace kalanis\kw_files\Processing\Storage;


use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_storage\Interfaces\IStorage;


/**
 * Class ProcessDir
 * @package kalanis\kw_files\Processing\Storage
 * Process dirs in storages
 */
class ProcessDir implements IProcessDirs
{
    /** @var IProcessDirs */
    protected $adapter = null;

    public function __construct(IStorage $storage)
    {
        $factory = new Dirs\Factory();
        $this->adapter = $factory->getClass($storage);
    }

    public function createDir(string $entry, bool $deep = false): bool
    {
        return $this->adapter->createDir($entry, $deep);
    }

    public function readDir(string $entry): array
    {
        return $this->adapter->readDir($entry);
    }

    public function copyDir(string $source, string $dest): bool
    {
        return $this->adapter->copyDir($source, $dest);
    }

    public function moveDir(string $source, string $dest): bool
    {
        return $this->adapter->moveDir($source, $dest);
    }

    public function deleteDir(string $entry, bool $deep = false): bool
    {
        return $this->adapter->deleteDir($entry, $deep);
    }
}
