<?php

namespace kalanis\kw_files\Processing\Storage\Dirs;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_files\Translations;
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

    /** @var IFLTranslations */
    protected $lang = null;
    /** @var IStorage */
    protected $storage = null;

    public function __construct(IStorage $storage, ?IFLTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->lang = $lang ?? new Translations();
    }

    public function createDir(array $entry, bool $deep = false): bool
    {
        $entryPath = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            if ($this->storage->exists($entryPath)) {
                return false;
            }
            $total = count($entry);
            for ($i = 1; $i < $total; $i++) {
                $subNodeName = $this->getStorageSeparator() . $this->compactName(array_slice($entry, 0, $i), $this->getStorageSeparator());
                $exists = $this->storage->exists($subNodeName);
                if ($exists) {
                    if (!$this->isNode($subNodeName)) {
                        // current node is file/data
                        return false;
                    }
                } else {
                    if ($deep) {
                        // create deep tree
                        $this->storage->write($subNodeName, static::STORAGE_NODE_KEY);
                    } else {
                        // cannot create in shallow tree
                        return false;
                    }
                }
            }
            return $this->storage->write($entryPath, static::STORAGE_NODE_KEY);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotCreateDir($entryPath), $ex->getCode(), $ex);
        }
    }

    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false, bool $withInitSub = false): array
    {
        $entryPath = $this->removeSeparator($this->compactName(array_filter($entry), $this->getStorageSeparator()));
        $entryPath = empty($entryPath) ? '' : $this->getStorageSeparator() . $entryPath;
        try {
            if (!$this->isNode($entryPath)) {
                throw new FilesException($this->lang->flCannotReadDir($entryPath));
            }
            /** @var array<string, Node> */
            $files = [];
            $sepLen = mb_strlen($this->getStorageSeparator());
            foreach ($this->storage->lookup($entryPath) as $item) {
                $usePath = mb_substr($item, $sepLen);
                if (!$loadRecursive && (false !== mb_strpos($usePath, $this->getStorageSeparator()))) {
                    // pass sub when not need
                    continue;
                }

                $sub = new Node();
                $currentPath = $this->removeSeparator($this->compactName(array_merge(
                    $entry,
                    $this->expandName($usePath, $this->getStorageSeparator())
                ), $this->getStorageSeparator()));
                if (empty($item)) {
                    $sub->setData(
                        array_merge([''], array_filter($entry)),
                        0,
                        ITypes::TYPE_DIR
                    );
                } elseif ($this->isNode($this->getStorageSeparator() . $currentPath)) {
                    $sub->setData(
                        $this->expandName($this->getStorageSeparator() . $currentPath),
                        0,
                        ITypes::TYPE_DIR
                    );
                } else {
                    // normal node - file
                    $sub->setData(
                        $this->expandName($this->getStorageSeparator() . $currentPath),
                        $wantSize ? $this->getSize($this->getStorageSeparator() . $currentPath) : 0,
                        ITypes::TYPE_FILE
                    );
                }
                $files[] = $sub;
            }
            return $files;
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotReadDir($entryPath), $ex->getCode(), $ex);
        }
    }

    protected function removeSeparator(string $path): string
    {
        $sepLen = mb_strlen($this->getStorageSeparator());
        $first = mb_substr($path, 0, $sepLen);
        return $this->getStorageSeparator() == $first ? mb_substr($path, $sepLen) : $path;
    }

    public function copyDir(array $source, array $dest): bool
    {
        $src = $this->getStorageSeparator() . $this->compactName($source, $this->getStorageSeparator());
        $dst = $this->getStorageSeparator() . $this->compactName($dest, $this->getStorageSeparator());
        try {
            if (!$this->isNode($src)) {
                return false;
            }
            if ($this->storage->exists($dst)) {
                return false;
            }
            $paths = $this->storage->lookup($src);
            $this->storage->write($dst, self::STORAGE_NODE_KEY);
            foreach ($paths as $path) {
                if (empty($path)) {
                    // skip current
                    continue;
                }
                $this->storage->write($dst . $path, $this->storage->read($src . $path));
            }
            return true;
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotCopyDir($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function moveDir(array $source, array $dest): bool
    {
        $src = $this->getStorageSeparator() . $this->compactName($source, $this->getStorageSeparator());
        $dst = $this->getStorageSeparator() . $this->compactName($dest, $this->getStorageSeparator());
        try {
            if (!$this->isNode($src)) {
                return false;
            }
            if ($this->storage->exists($dst)) {
                return false;
            }
            $paths = $this->storage->lookup($src);
            $this->storage->write($dst, self::STORAGE_NODE_KEY);
            foreach ($paths as $path) {
                if (empty($path)) {
                    // skip current
                    continue;
                }
                $this->storage->write($dst . $path, $this->storage->read($src . $path));
                $this->storage->remove($src . $path);
            }
            return $this->storage->remove($src);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotMoveDir($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function deleteDir(array $entry, bool $deep = false): bool
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            if (!$this->storage->exists($path)) {
                return false;
            }
            if (!$this->isNode($path)) {
                return false;
            }
            $paths = $this->storage->lookup($path);
            foreach ($paths as $item) {
                if ('' != $item) {
                    // found something
                    if (!$deep) {
                        return false;
                    }
                    $this->storage->remove($path . $item);
                }
            }
            return $this->storage->remove($path);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotRemoveDir($path), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string $entry
     * @throws StorageException
     * @return bool
     */
    protected function isNode(string $entry): bool
    {
        return $this->storage->exists($entry) ? (static::STORAGE_NODE_KEY === $this->storage->read($entry)) : false;
    }

    /**
     * @param string $file
     * @throws FilesException
     * @throws StorageException
     * @return int
     */
    protected function getSize(string $file): int
    {
        $content = $this->storage->read($file);
        if (is_resource($content)) {
            // a bit workaround
            $tempStream = fopen("php://temp", "w+b");
            if (false === $tempStream) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->lang->flCannotLoadFile($file));
            }
            // @codeCoverageIgnoreEnd
            rewind($content);
            $size = stream_copy_to_stream($content, $tempStream, -1, 0);
            if (false === $size) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->lang->flCannotGetSize($file));
            }
            // @codeCoverageIgnoreEnd
            return intval($size);
        } else {
            return mb_strlen(strval($content));
        }
    }
}
