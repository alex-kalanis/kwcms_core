<?php

namespace kalanis\kw_files\Processing\Storage\Dirs;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_files\Translations;
use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\Interfaces\IStorage;
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
    /** @var IPassDirs|IStorage */
    protected $storage = null;

    public function __construct(IPassDirs $storage, ?IFLTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->lang = $lang ?? new Translations();
    }

    public function createDir(array $entry, bool $deep = false): bool
    {
        $path = $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->mkDir($path, $deep);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotCreateDir($path), $ex->getCode(), $ex);
        }
    }

    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false): array
    {
        $entryPath = $this->compactName($entry, $this->getStorageSeparator());
        try {
            $files = [];
            foreach ($this->storage->lookup($entryPath) as $item) {
                $currentPath = $this->compactName($entry + [$item], $this->getStorageSeparator());
                $sub = new Node();
                if ($this->storage->isDir($currentPath)) {
                    $sub->setData(
                        $this->expandName($currentPath),
                        0,
                        ITypes::TYPE_DIR
                    );
                } else {
                    // normal node - file
                    $sub->setData(
                        $this->expandName($currentPath),
                        $wantSize ? intval($this->storage->size($currentPath)) : 0,
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

    public function copyDir(array $source, array $dest): bool
    {
        $src = $this->compactName($source, $this->getStorageSeparator());
        $dst = $this->compactName($dest, $this->getStorageSeparator());
        try {
            return $this->storage->copy($src, $dst);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotCopyDir($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function moveDir(array $source, array $dest): bool
    {
        $src = $this->compactName($source, $this->getStorageSeparator());
        $dst = $this->compactName($dest, $this->getStorageSeparator());
        try {
            return $this->storage->move($src, $dst);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotMoveDir($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function deleteDir(array $entry, bool $deep = false): bool
    {
        $path = $this->compactName($entry, $this->getStorageSeparator());
        try {
            return $this->storage->rmDir($path, $deep);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotRemoveDir($path), $ex->getCode(), $ex);
        }
    }
}
