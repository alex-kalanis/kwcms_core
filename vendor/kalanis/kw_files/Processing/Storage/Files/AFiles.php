<?php

namespace kalanis\kw_files\Processing\Storage\Files;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Processing\TNameFinder;
use kalanis\kw_files\Processing\TPathTransform;
use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class AFiles
 * @package kalanis\kw_files\Processing\Storage\Files
 * Process files in storages - deffer when you can access them directly or must be a middleman there
 */
abstract class AFiles implements IProcessFiles
{
    use TNameFinder;
    use TPathTransform;

    /** @var IFLTranslations */
    protected $lang = null;
    /** @var IStorage|IPassDirs */
    protected $storage = null;

    public function saveFile(array $targetName, $content): bool
    {
        $path = $this->getStorageSeparator() . $this->filledName($this->compactName($targetName, $this->getStorageSeparator()));
        try {
            return $this->storage->write($path, $content);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotSaveFile($path), $ex->getCode(), $ex);
        }
    }

    protected function getNameSeparator(): string
    {
        return static::FREE_NAME_SEPARATOR;
    }

    /**
     * @param array<string> $path
     * @param string $added
     * @throws FilesException
     * @return bool
     */
    protected function targetExists(array $path, string $added): bool
    {
        try {
            $path = $this->getStorageSeparator() . $this->compactName($path, $this->getStorageSeparator()) . $added;
            return $this->storage->exists($path);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null)
    {
        $path = $this->getStorageSeparator() . $this->filledName($this->compactName($entry, $this->getStorageSeparator()));
        try {
            $content = $this->storage->read($path);
            if (false === $content) {
                throw new FilesException($this->lang->flCannotLoadFile($path));
            } elseif (is_resource($content)) {
                if (!is_null($length) || !is_null($offset)) {
                    $stream = fopen('php://temp', 'rb+');
                    rewind($content);
                    if (false === $stream) {
                        // @codeCoverageIgnoreStart
                        throw new FilesException($this->lang->flCannotLoadFile($path));
                    }
                    // @codeCoverageIgnoreEnd
                    if (false === stream_copy_to_stream($content, $stream, (is_null($length) ? -1 : $length), intval($offset))) {
                        // @codeCoverageIgnoreStart
                        throw new FilesException($this->lang->flCannotGetFilePart($path));
                    }
                    // @codeCoverageIgnoreEnd
                    return $stream;
                } else {
                    return $content;
                }
            } else {
                // shit with substr... that needed undefined params was from some java dude?!
                if (!is_null($length)) {
                    return mb_substr(strval($content), intval($offset), $length);
                }
                if (!is_null($offset)) {
                    return mb_substr(strval($content), $offset);
                }
                return strval($content);
            }
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotLoadFile($path), $ex->getCode(), $ex);
        }
    }

    public function deleteFile(array $entry): bool
    {
        $path = $this->getStorageSeparator() . $this->filledName($this->compactName($entry, $this->getStorageSeparator()));
        try {
            return $this->storage->remove($path);
        } catch (StorageException $ex) {
            throw new FilesException($this->lang->flCannotRemoveFile($path), $ex->getCode(), $ex);
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
