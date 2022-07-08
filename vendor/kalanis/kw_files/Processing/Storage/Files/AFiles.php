<?php

namespace kalanis\kw_files\Processing\Storage\Files;


use kalanis\kw_files\FilesException;
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

    /** @var IStorage|IPassDirs */
    protected $storage = null;

    public function saveFile(array $targetName, $content): bool
    {
        try {
            return $this->storage->save($this->compactName($targetName, $this->getStorageSeparator()), $content);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    protected function getSeparator(): string
    {
        return static::FREE_NAME_SEPARATOR;
    }

    protected function targetExists(array $path, string $added): bool
    {
        return $this->storage->exists($this->compactName($path, $this->getStorageSeparator()) . $added);
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null)
    {
        try {
            $content = $this->storage->load($this->compactName($entry, $this->getStorageSeparator()));
            if (is_resource($content)) {
                if (!is_null($length) && !is_null($offset)) {
                    $stream = fopen('php://temp', 'rb+');
                    if (false === stream_copy_to_stream($content, $stream, $length, intval($offset))) {
                        throw new FilesException('Cannot extract stream part');
                    }
                    return $stream;
                } else {
                    return $content;
                }
            } else {
                // shit with substr... that needed undefined params was from some java dude?!
                if (!is_null($length) && !is_null($offset)) {
                    return mb_substr($content, $offset, $length);
                }
                if (is_null($length)) {
                    return mb_substr($content, null, $offset);
                }
                return strval($content);
            }
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function deleteFile(array $entry): bool
    {
        try {
            return $this->storage->remove($this->compactName($entry, $this->getStorageSeparator()));
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    protected function getStorageSeparator(): string
    {
        return DIRECTORY_SEPARATOR;
    }
}
