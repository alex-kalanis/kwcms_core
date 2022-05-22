<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_paths\Extras\TNameFinder;
use kalanis\kw_paths\Stuff;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;
use KWCMS\modules\Files\FilesException;
use KWCMS\modules\Files\Interfaces\IProcessFiles;


/**
 * Class ProcessStorageFile
 * @package KWCMS\modules\Files\Lib
 * Process files in many ways
 */
class ProcessStorageFile implements IProcessFiles
{
    use TNameFinder;

    protected $storage = null;
    protected $sourcePath = '';

    public function __construct(IStorage $storage, string $sourcePath)
    {
        $this->storage = $storage;
        $this->sourcePath = $sourcePath;
    }

    public function uploadFile(IFileEntry $file, string $targetName): bool
    {
        try {
            return $this->storage->save(
                $this->sourcePath . DIRECTORY_SEPARATOR . $targetName,
                file_get_contents($file->getTempName())
            );
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    protected function getSeparator(): string
    {
        return static::FREE_NAME_SEPARATOR;
    }

    protected function getTargetDir(): string
    {
        return $this->sourcePath;
    }

    protected function targetExists(string $path): bool
    {
        return $this->storage->exists($path);
    }

    public function readFile(string $entry, ?int $offset = null, ?int $length = null): string
    {
        try {
            $content = $this->storage->load($this->sourcePath . DIRECTORY_SEPARATOR . $entry);
            if (is_null($length) && !is_null($offset)) {
                return substr($content, $offset);
            } elseif (is_null($offset)) {
                return substr($content, 0, $length);
            } else {
                return substr($content, $offset, $length);
            }
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function copyFile(string $entry, string $to): bool
    {
        $fileName = Stuff::filename($entry);
        try {
            return $this->storage->save(
                strval($to) . DIRECTORY_SEPARATOR . $fileName,
                $this->storage->load($this->sourcePath . DIRECTORY_SEPARATOR . $entry)
            );
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function moveFile(string $entry, string $to): bool
    {
        $fileName = Stuff::filename($entry);
        try {
            $action1 = $this->storage->save(
                strval($to) . DIRECTORY_SEPARATOR . $fileName,
                $this->storage->load($this->sourcePath . DIRECTORY_SEPARATOR . $entry)
            );
            $action2 = $this->storage->remove(
                $this->sourcePath . DIRECTORY_SEPARATOR . $entry
            );
            return $action1 && $action2;
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function renameFile(string $entry, string $to): bool
    {
        try {
            $action1 = $this->storage->save(
                $this->sourcePath . DIRECTORY_SEPARATOR . $to,
                $this->storage->load($this->sourcePath . DIRECTORY_SEPARATOR . $entry)
            );
            $action2 = $this->storage->remove(
                $this->sourcePath . DIRECTORY_SEPARATOR . $entry
            );
            return $action1 && $action2;

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
