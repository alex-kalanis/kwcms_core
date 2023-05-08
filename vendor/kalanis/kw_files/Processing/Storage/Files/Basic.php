<?php

namespace kalanis\kw_files\Processing\Storage\Files;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
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
        $this->setLang($lang);
    }

    public function copyFile(array $source, array $dest): bool
    {
        $src = $this->getStorageSeparator() . $this->filledName($this->compactName($source, $this->getStorageSeparator()));
        $dst = $this->getStorageSeparator() . $this->filledName($this->compactName($dest, $this->getStorageSeparator()));
        try {
            return $this->storage->write($dst, $this->storage->read($src));
        } catch (StorageException $ex) {
            throw new FilesException($this->getLang()->flCannotCopyFile($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function moveFile(array $source, array $dest): bool
    {
        $v1 = $this->copyFile($source, $dest);
        $v2 = $this->deleteFile($source);
        return $v1 && $v2;
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
