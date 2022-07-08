<?php

namespace kalanis\kw_files\Processing\Storage\Dirs;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
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

    /** @var IStorage */
    protected $storage = null;

    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
    }

    public function createDir(array $entry, bool $deep = false): bool
    {
        try {
            $entryPath = $this->compactName($entry, $this->getStorageSeparator());
            if ($this->storage->exists($entryPath)) {
                return $this->isNode($entryPath);
            }
            $total = count($entry);
            for ($i = 1; $total < $i; $i++) {
                $subNodeName = $this->compactName(array_slice($entry, 0, $i), $this->getStorageSeparator());
                $exists = $this->storage->exists($subNodeName);
                if ($exists) {
                    if (!$this->isNode($subNodeName)) {
                        // current node is file/data
                        return false;
                    }
                } else {
                    if ($deep) {
                        // create deep tree
                        $this->storage->save($subNodeName, static::STORAGE_NODE_KEY);
                    } else {
                        // cannot create in shallow tree
                        return false;
                    }
                }
            }
            return $this->storage->save($entryPath, static::STORAGE_NODE_KEY);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false): array
    {
        try {
            $entryPath = $this->compactName($entry, $this->getStorageSeparator());
            if (!$this->storage->exists($entryPath) || !$this->isNode($entryPath)) {
                return [];
            }
            /** @var array<string, Node> */
            $files = [];
            $master = new Node();
            $master->setData($entry, 0, ITypes::TYPE_DIR);
            $files[$entryPath] = $master;
            foreach ($this->storage->lookup($entryPath) as $item) {
                if (!$loadRecursive && (false !== mb_strpos($item, $this->getStorageSeparator()))) {
                    // pass sub when not need
                    continue;
                }

                $sub = new Node();
                $currentPath = $this->compactName($entry + [$item], $this->getStorageSeparator());
                if ($this->isNode($currentPath)) {
                    $sub->setData(
                        $this->expandName($currentPath),
                        0,
                        ITypes::TYPE_DIR
                    );
                } else {
                    // normal node - file
                    $sub->setData(
                        $this->expandName($currentPath),
                        $wantSize ? $this->getSize($currentPath) : 0,
                        ITypes::TYPE_FILE
                    );
                }
                $files[] = $sub;
            }
            return $files;
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function copyDir(array $source, array $dest): bool
    {
        try {
            $source = $this->compactName($source, $this->getStorageSeparator());
            $dest = $this->compactName($dest, $this->getStorageSeparator());
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

    public function moveDir(array $source, array $dest): bool
    {
        try {
            $source = $this->compactName($source, $this->getStorageSeparator());
            $dest = $this->compactName($dest, $this->getStorageSeparator());
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

    public function deleteDir(array $entry, bool $deep = false): bool
    {
        try {
            $entry = $this->compactName($entry, $this->getStorageSeparator());
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

    /**
     * @param string $file
     * @throws FilesException
     * @throws StorageException
     * @return int
     */
    protected function getSize(string $file): int
    {
        $content = $this->storage->load($file);
        if (is_resource($content)) {
            // a bit workaround
            $tempStream = fopen("php://temp", "w+b");
            rewind($content);
            $size = stream_copy_to_stream($content, $tempStream, -1, 0);
            if (false === $size) {
                throw new FilesException('Cannot copy streams, cannot get sizes');
            }
            return intval($size);
        } else {
            return mb_strlen($content);
        }
    }
}
