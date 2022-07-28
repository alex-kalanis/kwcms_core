<?php

namespace kalanis\kw_files\Processing\Storage\Nodes;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Translations;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Basic
 * @package kalanis\kw_files\Processing\Storage\Nodes
 * Process dirs via lookup
 */
class Basic extends ANodes
{
    const STORAGE_NODE_KEY = "\eNODE\e";

    /** @var IFLTranslations */
    protected $lang = null;
    /** @var IStorage */
    protected $storage = null;

    public function __construct(IStorage $storage, ?IFLTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->lang = $lang ?? new Translations();
    }

    public function exists(array $entry): bool
    {
        $path = $this->compactName($entry, $this->getStorageSeparator());
        return $this->storage->exists($path);
    }

    public function isDir(array $entry): bool
    {
        try {
            $path = $this->compactName($entry, $this->getStorageSeparator());
            return static::STORAGE_NODE_KEY === $this->storage->load($path);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotProcessNode($path), $ex->getCode(), $ex);
        }
    }

    public function isFile(array $entry): bool
    {
        try {
            $path = $this->compactName($entry, $this->getStorageSeparator());
            return static::STORAGE_NODE_KEY !== $this->storage->load($path);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotProcessNode($path), $ex->getCode(), $ex);
        }
    }

    public function size(array $entry): ?int
    {
        try {
            $path = $this->compactName($entry, $this->getStorageSeparator());
            return strlen($this->storage->load($path));
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotProcessNode($path), $ex->getCode(), $ex);
        }
    }

    public function created(array $entry): ?int
    {
        return null;
    }
}
