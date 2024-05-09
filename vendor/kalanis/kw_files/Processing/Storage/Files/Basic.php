<?php

namespace kalanis\kw_files\Processing\Storage\Files;


use kalanis\kw_files\FilesException;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_storage\StorageException;


/**
 * Class Basic
 * @package kalanis\kw_files\Processing\Storage\Files
 * Process files via lookup
 */
class Basic extends AFiles
{
    public function copyFile(array $source, array $dest): bool
    {
        $src = $this->getStorageSeparator() . $this->filledName($this->compactName($source, $this->getStorageSeparator()));
        $dst = $this->getStorageSeparator() . $this->filledName($this->compactName($dest, $this->getStorageSeparator()));
        try {
            if ($this->storage->exists($dst)) {
                return false;
            }
            $dstArr = new ArrayPath();
            $dstArr->setArray($dest);
            $tgt = $this->compactName($dstArr->getArrayDirectory(), $this->getStorageSeparator());
            if (!empty($tgt) && !$this->storage->exists($this->getStorageSeparator() . $tgt)) {
                return false;
            }

            return $this->storage->write($dst, $this->storage->read($src));
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotCopyFile($src, $dst), $ex->getCode(), $ex);
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
        return $this->getFlLang()->flNoDirectoryDelimiterSet();
    }
}
