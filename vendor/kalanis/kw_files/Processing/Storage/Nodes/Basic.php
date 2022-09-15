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
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->exists($path);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotProcessNode($path), $ex->getCode(), $ex);
        }
    }

    public function isDir(array $entry): bool
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->exists($path) && static::STORAGE_NODE_KEY === $this->storage->read($path);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotProcessNode($path), $ex->getCode(), $ex);
        }
    }

    public function isFile(array $entry): bool
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->exists($path) && static::STORAGE_NODE_KEY !== $this->storage->read($path);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotProcessNode($path), $ex->getCode(), $ex);
        }
    }

    public function size(array $entry): ?int
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            if (!$this->storage->exists($path)) {
                return null;
            }
            $content = $this->storage->read($path);
            if (is_resource($content)) {
                // a bit workaround
                $tempStream = fopen("php://temp", "w+b");
                if (false === $tempStream) {
                    // @codeCoverageIgnoreStart
                    throw new FilesException($this->lang->flCannotLoadFile($path));
                }
                // @codeCoverageIgnoreEnd
                rewind($content);
                $size = stream_copy_to_stream($content, $tempStream, -1, 0);
                if (false === $size) {
                    // @codeCoverageIgnoreStart
                    throw new FilesException($this->lang->flCannotGetSize($path));
                }
                // @codeCoverageIgnoreEnd
                return intval($size);
            } else {
                return strlen(strval($content));
            }
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotProcessNode($path), $ex->getCode(), $ex);
        }
    }

    public function created(array $entry): ?int
    {
        return null;
    }
}
