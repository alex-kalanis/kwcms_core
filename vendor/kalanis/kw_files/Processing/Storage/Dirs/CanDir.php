<?php

namespace kalanis\kw_files\Processing\Storage\Dirs;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_files\Translations;
use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\StorageException;


/**
 * Class CanDir
 * @package kalanis\kw_files\Processing\Storage\Dirs
 * Process dirs via predefined api
 */
class CanDir extends ADirs
{
    /** @var IFLTranslations */
    protected $lang = null;
    /** @var IPassDirs */
    protected $storage = null;

    public function __construct(IPassDirs $storage, ?IFLTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->lang = $lang ?? new Translations();
    }

    public function createDir(array $entry, bool $deep = false): bool
    {
        $path = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->mkDir($path, $deep);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotCreateDir($path), $ex->getCode(), $ex);
        }
    }

    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false, bool $wantFirst = true): array
    {
        $entryPath = $this->getStorageSeparator() . $this->compactName($entry, $this->getStorageSeparator());
        try {
            if (!$this->storage->isDir($entryPath)) {
                throw new FilesException($this->lang->flCannotReadDir($entryPath));
            }
            $files = [];
            foreach ($this->storage->lookup($entryPath) as $item) {
                if ('..' == $item) {
                    continue;
                }
                $currentPath = $this->compactName(array_merge($entry, [$item]), $this->getStorageSeparator());
                $sub = new Node();
                if ('.' == $item) {
                    if (!$wantFirst) {
                        continue;
                    }

                    $sub->setData(
                        $entry,
                        0,
                        ITypes::TYPE_DIR
                    );
                } elseif ($this->storage->isDir($this->getStorageSeparator() . $currentPath)) {
                    $sub->setData(
                        $this->expandName($this->getStorageSeparator() . $currentPath),
                        0,
                        ITypes::TYPE_DIR
                    );
                } else {
                    // normal node - file
                    $sub->setData(
                        $this->expandName($this->getStorageSeparator() . $currentPath),
                        $wantSize ? intval($this->storage->size($currentPath)) : 0,
                        ITypes::TYPE_FILE
                    );
                }
                $files[] = $sub;
                if ($loadRecursive && $sub->isDir() && ('.' !== $item)) {
                    $files = array_merge($files, $this->readDir(array_merge($entry, [$item]), $loadRecursive, $wantSize, false));
                }
            }
            return $files;
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotReadDir($entryPath), $ex->getCode(), $ex);
        }
    }

    public function copyDir(array $source, array $dest): bool
    {
        $src = $this->getStorageSeparator() . $this->compactName($source, $this->getStorageSeparator());
        $dst = $this->getStorageSeparator() . $this->compactName($dest, $this->getStorageSeparator());
        try {
            return $this->storage->copy($src, $dst);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotCopyDir($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function moveDir(array $source, array $dest): bool
    {
        $src = $this->getStorageSeparator() . $this->compactName($source, $this->getStorageSeparator());
        $dst = $this->getStorageSeparator() . $this->compactName($dest, $this->getStorageSeparator());
        try {
            return $this->storage->move($src, $dst);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotMoveDir($src, $dst), $ex->getCode(), $ex);
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
            throw new FilesException($this->lang->flCannotRemoveDir($path), $ex->getCode(), $ex);
        }
    }
}
