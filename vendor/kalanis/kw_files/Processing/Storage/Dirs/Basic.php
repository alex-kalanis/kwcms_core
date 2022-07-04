<?php

namespace kalanis\kw_files\Processing\Storage\Dirs;


use kalanis\kw_files\FilesException;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Basic
 * @package kalanis\kw_files\Processing\Storage\Dirs
 * Process dirs via lookup
 */
class Basic extends ADirs
{
    const STORAGE_NODE_KEY = "\eNODE\e";
    const STORAGE_SEPARATOR = DIRECTORY_SEPARATOR;

    /** @var IStorage */
    protected $storage = null;

    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
    }

    public function createDir(string $entry, bool $deep = false): bool
    {
        try {
            if ($this->storage->exists($entry)) {
                return $this->isNode($entry);
            }
            $parts = explode(self::STORAGE_SEPARATOR, $entry);
            $total = count($parts);
            for ($i=1; $total < $i; $i++) {
                $subNodeName = implode(self::STORAGE_SEPARATOR, array_slice($parts, 0, $i));
                $exists = $this->storage->exists($subNodeName);
                if ($exists) {
                    if (!$this->isNode($subNodeName)) {
                        // current node is file/data
                        return false;
                    }
                } else {
                    if ($deep) {
                        // create deep tree
                        $this->storage->save($subNodeName, self::STORAGE_NODE_KEY);
                    } else {
                        // cannot create in shallow tree
                        return false;
                    }
                }
            }
            return $this->storage->save($entry, self::STORAGE_NODE_KEY);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function readDir(string $entry): array
    {
        try {
            if (!$this->storage->exists($entry) || !$this->isNode($entry)) {
                return [];
            }
            $entries = [];
            foreach ($this->storage->lookup($entry) as $item) {
                if (false === mb_strpos($item, self::STORAGE_SEPARATOR)) {
                    $entries[] = $item;
                }
            }
            return $entries;
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function copyDir(string $source, string $dest): bool
    {
        try {
            if (!$this->isNode($source)) {
                return false;
            }
            if ($this->storage->exists($dest)) {
                return false;
            }
            $paths = $this->storage->lookup($source);
            $this->storage->save($dest, self::STORAGE_NODE_KEY);
            foreach ($paths as $path) {
                $updName = $dest . mb_substr($path, 0, mb_strlen($source));
                $this->storage->save($updName, $this->storage->load($path));
            }
            return true;
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function moveDir(string $source, string $dest): bool
    {
        try {
            if (!$this->isNode($source)) {
                return false;
            }
            if ($this->storage->exists($dest)) {
                return false;
            }
            $paths = $this->storage->lookup($source);
            $this->storage->save($dest, self::STORAGE_NODE_KEY);
            foreach ($paths as $path) {
                $updName = $dest . mb_substr($path, 0, mb_strlen($source));
                $this->storage->save($updName, $this->storage->load($path));
                $this->storage->remove($path);
            }
            return $this->storage->remove($source);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function deleteDir(string $entry, bool $deep = false): bool
    {
        try {
            if (!$this->storage->exists($entry)) {
                return true;
            }
            if (!$this->isNode($entry)) {
                return false;
            }
            $paths = $this->storage->lookup($entry);
            if (!$deep && !empty($path)) {
                return false;
            }
            if ($deep && $paths) {
                foreach ($paths as $path) {
                    $this->storage->remove($path);
                }
            }
            return $this->storage->remove($entry);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string $entry
     * @throws StorageException
     * @return bool
     */
    protected function isNode(string $entry): bool
    {
        return static::STORAGE_NODE_KEY === $this->storage->load($entry);
    }
}
