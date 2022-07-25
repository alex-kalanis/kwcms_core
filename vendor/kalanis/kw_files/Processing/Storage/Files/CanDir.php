<?php

namespace kalanis\kw_files\Processing\Storage\Files;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Translations;
use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class CanDir
 * @package kalanis\kw_files\Processing\Storage\Files
 * Process files via predefined api
 */
class CanDir extends AFiles
{
    /** @var IStorage|IPassDirs */
    protected $storage = null;

    public function __construct(IPassDirs $storage, ?IFLTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->lang = $lang ?? new Translations();
    }

    public function copyFile(array $source, array $dest): bool
    {
        $src = $this->compactName($source, $this->getStorageSeparator());
        $dst = $this->compactName($dest, $this->getStorageSeparator());
        try {
            return $this->storage->copy($src, $dst);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotCopyFile($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function moveFile(array $source, array $dest): bool
    {
        $src = $this->compactName($source, $this->getStorageSeparator());
        $dst = $this->compactName($dest, $this->getStorageSeparator());
        try {
            return $this->storage->move($src, $dst);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotMoveFile($src, $dst), $ex->getCode(), $ex);
        }
    }
}
