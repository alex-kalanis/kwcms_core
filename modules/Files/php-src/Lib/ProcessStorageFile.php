<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_paths\Interfaces\IPaths;
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
    protected $storage = null;
    protected $sourcePath = '';

    public function __construct(IStorage $storage, string $sourcePath)
    {
        $this->storage = $storage;
        $this->sourcePath = $sourcePath;
    }

    public function uploadFile(IFileEntry $file): bool
    {
        try {
            return $this->storage->save(
                $this->sourcePath . DIRECTORY_SEPARATOR . $this->findFreeName($file->getValue()),
                file_get_contents($file->getTempName())
            );
        } catch (StorageException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    protected function findFreeName(string $name): string
    {
        $name = Stuff::canonize($name);
        $ext = Stuff::fileExt($name);
        if (0 < strlen($ext)) {
            $ext = IPaths::SPLITTER_DOT . $ext;
        }
        $fileName = Stuff::fileBase($name);
        if (!$this->storage->exists($this->sourcePath . $fileName . $ext)) {
            return $fileName . $ext;
        }
        $i = 0;
        while ($this->storage->exists($this->sourcePath . $fileName . static::FREE_NAME_SEPARATOR . $i . $ext)) {
            $i++;
        }
        return $fileName . static::FREE_NAME_SEPARATOR . $i . $ext;
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
