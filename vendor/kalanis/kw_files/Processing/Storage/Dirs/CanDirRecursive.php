<?php

namespace kalanis\kw_files\Processing\Storage\Dirs;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\StorageException;


/**
 * Class CanDirRecursive
 * @package kalanis\kw_files\Processing\Storage\Dirs
 * Process dirs via predefined api
 */
class CanDirRecursive extends ADirs
{
    /** @var IPassDirs */
    protected $storage = null;

    public function __construct(IPassDirs $storage, ?IFLTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->setLang($lang);
    }

    public function createDir(array $entry, bool $deep = false): bool
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->mkDir($path, $deep);
        } catch (StorageException $ex) {
            throw new FilesException($this->getLang()->flCannotCreateDir($path), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string[] $entry
     * @param bool $loadRecursive
     * @param bool $wantSize
     * @param string[] $previousPaths
     * @throws FilesException
     * @throws PathsException
     * @return Node[]
     */
    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false, array $previousPaths = []): array
    {
        $entryPath = $this->compactName($entry, $this->getStorageSeparator());
        try {
            if (!$this->storage->isDir($this->getStorageSeparator() . $entryPath)) {
                throw new FilesException($this->getLang()->flCannotReadDir($entryPath));
            }
            $files = [];
            $sepLen = mb_strlen($this->getStorageSeparator());
            foreach ($this->storage->lookup($this->getStorageSeparator() . $entryPath) as $item) {
                $usePath = mb_substr($item, $sepLen);
                $currentPath = $this->compactName(array_merge($entry, [$usePath]), $this->getStorageSeparator());
                $sub = new Node();
                if ('' == $item) {
                    if (!empty($previousPaths)) {
                        continue;
                    }

                    $sub->setData(
                        [],
                        0,
                        ITypes::TYPE_DIR
                    );
                } elseif ($this->storage->isDir($this->getStorageSeparator() . $currentPath)) {
                    $sub->setData(
                        array_merge($previousPaths, $this->expandName($usePath)),
                        0,
                        ITypes::TYPE_DIR
                    );
                } else {
                    // normal node - file
                    $sub->setData(
                        array_merge($previousPaths, $this->expandName($usePath)),
                        $wantSize ? intval($this->storage->size($this->getStorageSeparator() . $currentPath)) : 0,
                        ITypes::TYPE_FILE
                    );
                }
                $files[] = $sub;
                if ($loadRecursive && $sub->isDir() && ('' !== $item)) {
                    $files = array_merge($files, $this->readDir(array_merge($entry, $sub->getPath()), $loadRecursive, $wantSize, $sub->getPath()));
                }
            }
            return $files;
        } catch (StorageException $ex) {
            throw new FilesException($this->getLang()->flCannotReadDir($entryPath), $ex->getCode(), $ex);
        }
    }

    public function copyDir(array $source, array $dest): bool
    {
        $src = $this->getStorageSeparator() . $this->compactName($source, $this->getStorageSeparator());
        $dst = $this->getStorageSeparator() . $this->compactName($dest, $this->getStorageSeparator());
        try {
            if ($this->isSubPart($dest, $source)) {
                return false;
            }

            if (!$this->isNode($src)) {
                return false;
            }

            if ($this->storage->exists($dst)) {
                return false;
            }
            $dstArr = new ArrayPath();
            $dstArr->setArray($dest);
            if (!$this->storage->exists($this->getStorageSeparator() . $this->compactName($dstArr->getArrayDirectory(), $this->getStorageSeparator()))) {
                return false;
            }

            return $this->storage->copy($src, $dst);
        } catch (StorageException $ex) {
            throw new FilesException($this->getLang()->flCannotCopyDir($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function moveDir(array $source, array $dest): bool
    {
        $src = $this->getStorageSeparator() . $this->compactName($source, $this->getStorageSeparator());
        $dst = $this->getStorageSeparator() . $this->compactName($dest, $this->getStorageSeparator());
        try {
            if ($this->isSubPart($dest, $source)) {
                return false;
            }

            if (!$this->isNode($src)) {
                return false;
            }

            if ($this->storage->exists($dst)) {
                return false;
            }
            $dstArr = new ArrayPath();
            $dstArr->setArray($dest);
            if (!$this->storage->exists($this->getStorageSeparator() . $this->compactName($dstArr->getArrayDirectory(), $this->getStorageSeparator()))) {
                return false;
            }

            return $this->storage->move($src, $dst);
        } catch (StorageException $ex) {
            throw new FilesException($this->getLang()->flCannotMoveDir($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function deleteDir(array $entry, bool $deep = false): bool
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            if ($this->storage->isDir($path)) {
                return $this->storage->rmDir($path, $deep);
            } else {
                return false;
            }
        } catch (StorageException $ex) {
            throw new FilesException($this->getLang()->flCannotRemoveDir($path), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string $entry
     * @throws StorageException
     * @return bool
     */
    protected function isNode(string $entry): bool
    {
        return $this->storage->isDir($entry);
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
