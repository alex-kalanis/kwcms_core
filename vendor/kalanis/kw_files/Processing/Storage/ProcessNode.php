<?php

namespace kalanis\kw_files\Processing\Storage;


use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessNodes;
use kalanis\kw_storage\Interfaces\IStorage;


/**
 * Class ProcessNode
 * @package kalanis\kw_files\Processing\Storage
 * Process nodes in storages
 */
class ProcessNode implements IProcessNodes
{
    /** @var IProcessNodes */
    protected $adapter = null;

    public function __construct(IStorage $storage, ?IFLTranslations $lang = null)
    {
        $factory = new Nodes\Factory();
        $this->adapter = $factory->getClass($storage, $lang);
    }

    public function exists(array $entry): bool
    {
        return $this->adapter->exists($entry);
    }

    public function isDir(array $entry): bool
    {
        return $this->adapter->isDir($entry);
    }

    public function isFile(array $entry): bool
    {
        return $this->adapter->isFile($entry);
    }

    public function size(array $entry): ?int
    {
        return $this->adapter->size($entry);
    }

    public function created(array $entry): ?int
    {
        return $this->adapter->created($entry);
    }
}
