<?php

namespace KWCMS\modules\Files\Lib;


use Error;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Files\FilesException;
use KWCMS\modules\Files\Interfaces\IProcessDirs;


/**
 * Class ProcessDir
 * @package KWCMS\modules\Files\Lib
 * Process dirs in basic ways
 */
class ProcessDir implements IProcessDirs
{
    protected $sourcePath = '';
    protected $currentDir = '';

    public function __construct(string $sourcePath, string $currentDir)
    {
        $this->sourcePath = $sourcePath;
        $this->currentDir = $currentDir;
//var_dump(['proc file', $sourcePath, $currentDir]);
    }

    public function createDir(string $entry): bool
    {
        try {
            return mkdir($this->sourcePath . $this->currentDir . Stuff::filename($entry));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function copyDir(string $entry, string $to): bool
    {
        $fileName = Stuff::filename($entry);
        try {
            return copy(
                $this->sourcePath . $this->currentDir . $entry,
                $this->sourcePath . strval($to) . DIRECTORY_SEPARATOR . $fileName
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function moveDir(string $entry, string $to): bool
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

    public function renameDir(string $entry, string $to): bool
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

    public function deleteDir(string $entry): bool
    {
        try {
            return rmdir(
                $this->sourcePath . $this->currentDir . $entry
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
