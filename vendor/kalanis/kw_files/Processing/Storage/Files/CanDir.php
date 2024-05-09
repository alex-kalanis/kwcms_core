<?php

namespace kalanis\kw_files\Processing\Storage\Files;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\StorageException;


/**
 * Class CanDir
 * @package kalanis\kw_files\Processing\Storage\Files
 * Process files via predefined api
 */
class CanDir extends AFiles
{
    protected IPassDirs $passStorage;

    public function __construct(IPassDirs $storage, ?IFLTranslations $lang = null)
    {
        parent::__construct($storage, $lang);
        $this->passStorage = $storage;
    }

    public function copyFile(array $source, array $dest): bool
    {
        $src = $this->getStorageSeparator() . $this->compactName($source, $this->getStorageSeparator());
        $dst = $this->getStorageSeparator() . $this->compactName($dest, $this->getStorageSeparator());
        try {
            return $this->passStorage->copy($src, $dst);
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotCopyFile($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function moveFile(array $source, array $dest): bool
    {
        $src = $this->getStorageSeparator() . $this->compactName($source, $this->getStorageSeparator());
        $dst = $this->getStorageSeparator() . $this->compactName($dest, $this->getStorageSeparator());
        try {
            return $this->passStorage->move($src, $dst);
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotMoveFile($src, $dst), $ex->getCode(), $ex);
        }
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
