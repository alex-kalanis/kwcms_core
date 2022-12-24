<?php

namespace kalanis\kw_files\Processing\Storage;


use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_storage\Interfaces\IStorage;


/**
 * Class ProcessFile
 * @package kalanis\kw_files\Processing\Storage
 * Process files in many ways
 */
class ProcessFile implements IProcessFiles
{
    /** @var IProcessFiles */
    protected $adapter = null;

    public function __construct(IStorage $storage, ?IFLTranslations $lang = null)
    {
        $factory = new Files\Factory();
        $this->adapter = $factory->getClass($storage, $lang);
    }

    public function findFreeName(array $path, string $name, string $suffix): string
    {
        return $this->adapter->findFreeName($path, $name, $suffix);
    }

    public function saveFile(array $entry, $content): bool
    {
        return $this->adapter->saveFile($entry, $content);
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null)
    {
        return $this->adapter->readFile($entry, $offset, $length);
    }

    public function copyFile(array $source, array $dest): bool
    {
        return $this->adapter->copyFile($source, $dest);
    }

    public function moveFile(array $source, array $dest): bool
    {
        return $this->adapter->moveFile($source, $dest);
    }

    public function deleteFile(array $entry): bool
    {
        return $this->adapter->deleteFile($entry);
    }
}
