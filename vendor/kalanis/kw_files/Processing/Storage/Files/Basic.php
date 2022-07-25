<?php

namespace kalanis\kw_files\Processing\Storage\Files;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Translations;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Basic
 * @package kalanis\kw_files\Processing\Storage\Files
 * Process files via lookup
 */
class Basic extends AFiles
{
    public function __construct(IStorage $storage, ?IFLTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->lang = $lang ?? new Translations();
    }

    public function copyFile(array $source, array $dest): bool
    {
        $src = $this->compactName($source, $this->getStorageSeparator());
        $dst = $this->compactName($dest, $this->getStorageSeparator());
        try {
            return $this->storage->save($dst, $this->storage->load($src));
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotCopyFile($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function moveFile(array $source, array $dest): bool
    {
        $this->copyFile($source, $dest);
        $this->deleteFile($source);
        return true;
    }
}
