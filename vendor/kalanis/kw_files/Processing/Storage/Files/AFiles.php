<?php

namespace kalanis\kw_files\Processing\Storage\Files;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces;
use kalanis\kw_files\Traits\TCheckModes;
use kalanis\kw_files\Traits\TLang;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Extras\TPathTransform;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class AFiles
 * @package kalanis\kw_files\Processing\Storage\Files
 * Process files in storages - deffer when you can access them directly or must be a middleman there
 */
abstract class AFiles implements Interfaces\IProcessFiles
{
    use TCheckModes;
    use TLang;
    use TPathTransform;

    protected IStorage $storage;

    public function __construct(IStorage $storage, ?Interfaces\IFLTranslations $lang = null)
    {
        $this->storage = $storage;
        $this->setFlLang($lang);
    }

    public function saveFile(array $targetName, string $content, ?int $offset = null, int $mode = 0): bool
    {
        $this->checkSupportedModes($mode);
        $path = $this->getStorageSeparator() . $this->filledName($this->compactName($targetName, $this->getStorageSeparator()));
        try {
            $dstArr = new ArrayPath();
            $dstArr->setArray($targetName);
            $tgt = $this->compactName($dstArr->getArrayDirectory(), $this->getStorageSeparator());
            if (!empty($tgt) && !$this->storage->exists($this->getStorageSeparator() . $tgt)) {
                // parent dir
                throw new FilesException($this->getFlLang()->flCannotSaveFile($path));
            }

            $target = '';
            if (FILE_APPEND == $mode) {
                if ($this->storage->exists($path)) {
                    $target = $this->storage->read($path);
                }
            }

            if (!is_null($offset)) {
                // put it somewhere, left the rest intact
                $target = str_pad(substr($target, 0, $offset), $offset, "\0");
            }
            return $this->storage->write($path, $target . $content);
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotSaveFile($path), $ex->getCode(), $ex);
        }
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null): string
    {
        $path = $this->getStorageSeparator() . $this->filledName($this->compactName($entry, $this->getStorageSeparator()));
        try {
            $content = $this->storage->read($path);
            // shit with substr... that needed undefined params was from some java dude?!
            if (!is_null($length)) {
                return mb_substr($content, intval($offset), $length);
            }
            if (!is_null($offset)) {
                return mb_substr($content, $offset);
            }
            return $content;
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotLoadFile($path), $ex->getCode(), $ex);
        }
    }

    public function deleteFile(array $entry): bool
    {
        $path = $this->getStorageSeparator() . $this->filledName($this->compactName($entry, $this->getStorageSeparator()));
        try {
            return $this->storage->remove($path);
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotRemoveFile($path), $ex->getCode(), $ex);
        }
    }

    protected function getStorageSeparator(): string
    {
        return DIRECTORY_SEPARATOR;
    }

    protected function filledName(string $path): string
    {
        $sepLen = mb_strlen($this->getStorageSeparator());
        $beginning = mb_substr($path, 0, $sepLen);
        return ($this->getStorageSeparator() == $beginning) ? mb_substr($path, $sepLen) : $path;
    }
}
