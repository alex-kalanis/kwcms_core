<?php

namespace kalanis\kw_files\Processing\Storage\Files;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Processing\TNameFinder;
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

    /** @var IStorage|IPassDirs */
    protected $storage = null;

    public function saveFile(string $targetName, $content): bool
    {
        try {
            return $this->storage->save($targetName, $content);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    protected function getSeparator(): string
    {
        return static::FREE_NAME_SEPARATOR;
    }

    protected function targetExists(string $path): bool
    {
        return $this->storage->exists($path);
    }

    public function readFile(string $entry, ?int $offset = null, ?int $length = null): string
    {
        try {
            $content = $this->storage->load($entry);
            if (is_resource($content)) {
                return stream_get_contents(
                    $content,
                    is_null($length) ? -1 : intval($length),
                    intval($offset)
                );
            } else {
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

    public function deleteFile(string $entry): bool
    {
        try {
            return $this->storage->remove($entry);
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
