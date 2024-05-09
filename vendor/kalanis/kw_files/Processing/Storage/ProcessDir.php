<?php

namespace kalanis\kw_files\Processing\Storage;


use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_storage\Interfaces\IStorage;


/**
 * Class ProcessDir
 * @package kalanis\kw_files\Processing\Storage
 * Process dirs in storages
 */
class ProcessDir implements IProcessDirs
{
    protected IProcessDirs $adapter;

    public function __construct(IStorage $storage, ?IFLTranslations $lang = null)
    {
        $factory = new Dirs\Factory();
        $this->adapter = $factory->getClass($storage, $lang);
    }

    public function createDir(array $entry, bool $deep = false): bool
    {
        return $this->adapter->createDir($entry, $deep);
    }

    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false): array
    {
        return $this->adapter->readDir($entry, $loadRecursive, $wantSize);
    }

    public function copyDir(array $source, array $dest): bool
    {
        return $this->adapter->copyDir($source, $dest);
    }

    public function moveDir(array $source, array $dest): bool
    {
        return $this->adapter->moveDir($source, $dest);
    }

    public function deleteDir(array $entry, bool $deep = false): bool
    {
        return $this->adapter->deleteDir($entry, $deep);
    }
}
