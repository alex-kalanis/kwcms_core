<?php

namespace kalanis\kw_files\Processing\Storage\Nodes;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Basic
 * @package kalanis\kw_files\Processing\Storage\Nodes
 * Process dirs via lookup
 */
class Basic extends ANodes
{
    use TToString;

    protected IStorage $storage;

    public function __construct(IStorage $storage, ?IFLTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->setFlLang($lang);
    }

    public function exists(array $entry): bool
    {
        $path = $this->compactName($entry, $this->getStorageSeparator());
        $path = empty($entry) ? $path : $this->getStorageSeparator() . $path;
        try {
            return $this->storage->exists($path);
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotProcessNode($path), $ex->getCode(), $ex);
        }
    }

    public function isReadable(array $entry): bool
    {
        return true;
    }

    public function isWritable(array $entry): bool
    {
        return true;
    }

    public function isDir(array $entry): bool
    {
        $path = $this->compactName($entry, $this->getStorageSeparator());
        $path = empty($entry) ? $path : $this->getStorageSeparator() . $path;
        try {
            return $this->storage->exists($path) && static::STORAGE_NODE_KEY === $this->toString($path, $this->storage->read($path));
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotProcessNode($path), $ex->getCode(), $ex);
        }
    }

    public function isFile(array $entry): bool
    {
        $path = $this->compactName($entry, $this->getStorageSeparator());
        $path = empty($entry) ? $path : $this->getStorageSeparator() . $path;
        try {
            return $this->storage->exists($path) && static::STORAGE_NODE_KEY !== $this->toString($path, $this->storage->read($path));
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotProcessNode($path), $ex->getCode(), $ex);
        }
    }

    public function size(array $entry): ?int
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            if (!$this->storage->exists($path)) {
                return null;
            }
            return strlen($this->storage->read($path));
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotProcessNode($path), $ex->getCode(), $ex);
        }
    }

    public function created(array $entry): ?int
    {
        return null;
    }

    /**
     * @return string
     * @codeCoverageIgnore only when path fails
     */
    protected function noDirectoryDelimiterSet(): string
    {
        return $this->getFlLang()->flNoDirectoryDelimiterSet();
    }
}
