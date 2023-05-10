<?php

namespace kalanis\kw_files\Processing\Storage\Nodes;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Basic
 * @package kalanis\kw_files\Processing\Storage\Nodes
 * Process dirs via lookup
 */
class Basic extends ANodes
{
    /** @var IStorage */
    protected $storage = null;

    public function __construct(IStorage $storage, ?IFLTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->setLang($lang);
    }

    public function exists(array $entry): bool
    {
        $path = $this->compactName($entry, $this->getStorageSeparator());
        $path = empty($entry) ? $path : $this->getStorageSeparator() . $path;
        try {
            return $this->storage->exists($path);
        } catch (StorageException $ex) {
            throw new FilesException($this->getLang()->flCannotProcessNode($path), $ex->getCode(), $ex);
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
            return $this->storage->exists($path) && static::STORAGE_NODE_KEY === $this->storage->read($path);
        } catch (StorageException $ex) {
            throw new FilesException($this->getLang()->flCannotProcessNode($path), $ex->getCode(), $ex);
        }
    }

    public function isFile(array $entry): bool
    {
        $path = $this->compactName($entry, $this->getStorageSeparator());
        $path = empty($entry) ? $path : $this->getStorageSeparator() . $path;
        try {
            return $this->storage->exists($path) && static::STORAGE_NODE_KEY !== $this->storage->read($path);
        } catch (StorageException $ex) {
            throw new FilesException($this->getLang()->flCannotProcessNode($path), $ex->getCode(), $ex);
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
                    throw new FilesException($this->getLang()->flCannotLoadFile($path));
                }
                // @codeCoverageIgnoreEnd
                rewind($content);
                $size = stream_copy_to_stream($content, $tempStream, -1, 0);
                if (false === $size) {
                    // @codeCoverageIgnoreStart
                    throw new FilesException($this->getLang()->flCannotGetSize($path));
                }
                // @codeCoverageIgnoreEnd
                return intval($size);
            } else {
                return strlen(strval($content));
            }
        } catch (StorageException $ex) {
            throw new FilesException($this->getLang()->flCannotProcessNode($path), $ex->getCode(), $ex);
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
        return $this->getLang()->flNoDirectoryDelimiterSet();
    }
}
