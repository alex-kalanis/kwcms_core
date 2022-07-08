<?php

namespace kalanis\kw_files\Processing\Volume;


use Error;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Processing\TNameFinder;
use kalanis\kw_files\Processing\TPathTransform;
use kalanis\kw_files\Translations;


/**
 * Class ProcessFile
 * @package kalanis\kw_files\Processing\Volume
 * Process files in many ways
 */
class ProcessFile implements IProcessFiles
{
    use TNameFinder;
    use TPathTransform;

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

    protected function targetExists(array $path, string $added): bool
    {
        return @file_exists($this->compactName($path) . $added);
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null)
    {
        try {
            if (!is_null($length) && is_null($offset)) {
                $content = @file_get_contents($this->compactName($entry), false, null, 0, $length);
            } elseif (is_null($offset)) {
                $content = @file_get_contents($this->compactName($entry));
            } else {
                $content = @file_get_contents($this->compactName($entry), false, null, $offset, $length);
            }
            if (false !== $content) {
                return $content;
            }
            throw new FilesException($this->lang->flCannotLoadFile($this->compactName($entry)));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function saveFile(array $entry, $content): bool
    {
        try {
            $result = @file_put_contents($this->compactName($entry), $content);
            if (false === $result) {
                throw new FilesException($this->lang->flCannotSaveFile($this->compactName($entry)));
            }
            return true;
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function copyFile(array $source, array $dest): bool
    {
        try {
            return @copy($this->compactName($source), $this->compactName($dest));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function moveFile(array $source, array $dest): bool
    {
        try {
            return @rename($this->compactName($source), $this->compactName($dest));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function deleteFile(array $entry): bool
    {
        try {
            return @unlink($this->compactName($entry));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
