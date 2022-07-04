<?php

namespace kalanis\kw_files\Processing\Volume;


use Error;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Processing\TNameFinder;
use kalanis\kw_files\Translations;


/**
 * Class ProcessFile
 * @package kalanis\kw_files\Processing\Volume
 * Process files in many ways
 */
class ProcessFile implements IProcessFiles
{
    use TNameFinder;

    /** @var IFLTranslations */
    protected $lang = null;

    public function __construct(?IFLTranslations $lang = null)
    {
        $this->lang = $lang ?? new Translations();
    }

    protected function getSeparator(): string
    {
        return static::FREE_NAME_SEPARATOR;
    }

    protected function targetExists(string $path): bool
    {
        return file_exists($path);
    }

    public function readFile(string $entry, ?int $offset = null, ?int $length = null): string
    {
        try {
            if (!is_null($length) && is_null($offset)) {
                $content = @file_get_contents($entry, false, null, 0, $length);
            } elseif (is_null($offset)) {
                $content = @file_get_contents($entry);
            } else {
                $content = @file_get_contents($entry, false, null, $offset, $length);
            }
            if (false !== $content) {
                return $content;
            }
            throw new FilesException($this->lang->flCannotLoadFile($entry));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function saveFile(string $entry, $content): bool
    {
        try {
            $result = @file_put_contents($entry, $content);
            if (false === $result) {
                throw new FilesException($this->lang->flCannotSaveFile($entry));
            }
            return true;
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function copyFile(string $source, string $dest): bool
    {
        try {
            return copy($source, $dest);
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function moveFile(string $source, string $dest): bool
    {
        try {
            return rename($source, $dest);
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function deleteFile(string $entry): bool
    {
        try {
            return unlink($entry);
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
