<?php

namespace KWCMS\modules\Files\Lib;


use Error;
use kalanis\kw_files\FilesException;
use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_paths\Extras\TNameFinder;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Files\Interfaces\IProcessFiles;


/**
 * Class ProcessFile
 * @package KWCMS\modules\Files\Lib
 * Process files in many ways
 */
class ProcessFile implements IProcessFiles
{
    use TNameFinder;

    protected $sourcePath = '';
    protected $currentDir = '';

    public function __construct(string $sourcePath, string $currentDir)
    {
        $this->sourcePath = $sourcePath;
        $this->currentDir = $currentDir;
    }

    public function uploadFile(IFileEntry $file, string $targetName): bool
    {
        try {
            return move_uploaded_file($file->getTempName(), $this->sourcePath . $this->currentDir . $targetName);
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    protected function getSeparator(): string
    {
        return static::FREE_NAME_SEPARATOR;
    }

    protected function getTargetDir(): string
    {
        return $this->sourcePath . $this->currentDir;
    }

    protected function targetExists(string $path): bool
    {
        return file_exists($path);
    }

    public function readFile(string $entry, ?int $offset = null, ?int $length = null): string
    {
        try {
            if (!is_null($length) && is_null($offset)) {
                $content = @file_get_contents($this->sourcePath  . $this->currentDir . $entry,
                false, null, 0, $length);
            } elseif (is_null($offset)) {
                $content = @file_get_contents($this->sourcePath  . $this->currentDir . $entry);
            } else {
                $content = @file_get_contents($this->sourcePath  . $this->currentDir . $entry,
                false, null, $offset, $length);
            }
            if (false !== $content) {
                return $content;
            }
            throw new FilesException(Lang::get('files.file.read.not_load'));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
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
