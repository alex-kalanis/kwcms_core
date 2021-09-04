<?php

namespace KWCMS\modules\Files\Lib;


use Error;
use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Files\FilesException;
use KWCMS\modules\Files\Interfaces\IProcessFiles;


/**
 * Class ProcessFile
 * @package KWCMS\modules\Files\Lib
 * Process files in many ways
 */
class ProcessFile implements IProcessFiles
{
    protected $sourcePath = '';
    protected $currentDir = '';

    public function __construct(string $sourcePath, string $currentDir)
    {
        $this->sourcePath = $sourcePath;
        $this->currentDir = $currentDir;
//var_dump(['proc file', $sourcePath, $currentDir]);
    }

    public function uploadFile(IFileEntry $file): bool
    {
        try {
            return move_uploaded_file($file->getTempName(), $this->sourcePath . $this->currentDir . $this->findFreeName($file->getValue()));
        } catch (Error $ex) {
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
        if (!file_exists($this->sourcePath . $this->currentDir . $fileName . $ext)) {
            return $fileName . $ext;
        }
        $i = 0;
        while (file_exists($this->sourcePath . $this->currentDir . $fileName . static::FREE_NAME_SEPARATOR . $i . $ext)) {
            $i++;
        }
        return $fileName . static::FREE_NAME_SEPARATOR . $i . $ext;
    }

    public function copyFile(string $entry, string $to): bool
    {
        $fileName = Stuff::filename($entry);
        try {
            return copy(
                $this->sourcePath  . $this->currentDir . $entry,
                $this->sourcePath . strval($to) . DIRECTORY_SEPARATOR . $fileName
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function moveFile(string $entry, string $to): bool
    {
        $fileName = Stuff::filename($entry);
        try {
            return rename(
                $this->sourcePath . $this->currentDir . $entry,
                $this->sourcePath . strval($to) . DIRECTORY_SEPARATOR . $fileName
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function renameFile(string $entry, string $to): bool
    {
        try {
            return rename(
                $this->sourcePath . $this->currentDir . $entry,
                $this->sourcePath . $this->currentDir . $to
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function deleteFile(string $entry): bool
    {
        try {
            return unlink(
                $this->sourcePath . $this->currentDir . $entry
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
