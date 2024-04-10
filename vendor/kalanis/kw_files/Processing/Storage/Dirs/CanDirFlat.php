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
 * Class CanDirDirect
 * @package kalanis\kw_files\Processing\Storage\Dirs
 * Process dirs via predefined api
 */
class CanDirFlat extends ADirs
{
    protected IPassDirs $storage;

    public function __construct(IPassDirs $storage, ?IFLTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->setFlLang($lang);
    }

    public function createDir(array $entry, bool $deep = false): bool
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->mkDir($path, $deep);
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotCreateDir($path), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string[] $entry
     * @param bool $loadRecursive
     * @param bool $wantSize
     * @throws FilesException
     * @throws PathsException
     * @return Node[]
     */
    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false): array
    {
        $entryPath = $this->removeSeparator($this->compactName(array_filter($entry), $this->getStorageSeparator()));
        $entryPath = empty($entryPath) ? '' : $this->getStorageSeparator() . $entryPath;
        try {
            if (!$this->isNode($entryPath)) {
                throw new FilesException($this->getFlLang()->flCannotReadDir($entryPath));
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
                    $this->expandName($item, $this->getStorageSeparator())
                ), $this->getStorageSeparator()));
                if (empty($item)) {
                    $sub->setData(
                        [],
                        0,
                        ITypes::TYPE_DIR
                    );
                } elseif ($this->isNode($this->getStorageSeparator(). $currentPath)) {
                    $sub->setData(
                        $this->expandName($usePath),
                        0,
                        ITypes::TYPE_DIR
                    );
                } else {
                    // normal node - file
                    $sub->setData(
                        $this->expandName($usePath),
                        $wantSize ? $this->getSize($this->getStorageSeparator() . $currentPath) : 0,
                        ITypes::TYPE_FILE
                    );
                }
                $files[] = $sub;
            }
            return $files;
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotReadDir($entryPath), $ex->getCode(), $ex);
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
            throw new FilesException($this->getFlLang()->flCannotCopyDir($src, $dst), $ex->getCode(), $ex);
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
            throw new FilesException($this->getFlLang()->flCannotMoveDir($src, $dst), $ex->getCode(), $ex);
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
            throw new FilesException($this->getFlLang()->flCannotRemoveDir($path), $ex->getCode(), $ex);
        }
    }

    protected function removeSeparator(string $path): string
    {
        $sepLen = mb_strlen($this->getStorageSeparator());
        $first = mb_substr($path, 0, $sepLen);
        return $this->getStorageSeparator() == $first ? mb_substr($path, $sepLen) : $path;
    }

    /**
     * @param string $entry
     * @throws StorageException
     * @return bool
     */
    protected function isNode(string $entry): bool
    {
        return $this->storage->exists($entry) && $this->storage->isDir($entry);
    }

    /**
     * @param string $file
     * @throws StorageException
     * @return int
     */
    protected function getSize(string $file): int
    {
        return strlen(strval($this->storage->read($file)));
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
