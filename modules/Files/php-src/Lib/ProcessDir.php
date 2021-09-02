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

    public function __construct(string $sourcePath)
    {
        $this->sourcePath = $sourcePath;
    }

    public function newDir(string $entry): bool
    {
        try {
            return mkdir($this->sourcePath . DIRECTORY_SEPARATOR . Stuff::filename($entry));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function copyDir(string $entry, string $to): bool
    {
        $fileName = Stuff::filename($entry);
        try {
            return copy(
                $this->sourcePath . DIRECTORY_SEPARATOR . $entry,
                strval($to) . DIRECTORY_SEPARATOR . $fileName
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
                $this->sourcePath . DIRECTORY_SEPARATOR . $entry,
                strval($to) . DIRECTORY_SEPARATOR . $fileName
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function renameDir(string $entry, string $to): bool
    {
        try {
            return rename(
                $this->sourcePath . DIRECTORY_SEPARATOR . $entry,
                $this->sourcePath . DIRECTORY_SEPARATOR . $to
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function deleteDir(string $entry): bool
    {
        try {
            return rmdir(
                $this->sourcePath . DIRECTORY_SEPARATOR . $entry
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
