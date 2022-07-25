<?php

namespace kalanis\kw_files\Processing\Volume;


use Error;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Processing\TNameFinder;
use kalanis\kw_files\Processing\TPath;
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
    use TPath;
    use TPathTransform;

    /** @var IFLTranslations */
    protected $lang = null;

    public function __construct(string $path = '', ?IFLTranslations $lang = null)
    {
        $this->lang = $lang ?? new Translations();
        $this->setPath($path);
    }

    protected function getSeparator(): string
    {
        return static::FREE_NAME_SEPARATOR;
    }

    protected function targetExists(array $path, string $added): bool
    {
        return @file_exists($this->fullPath($path) . $added);
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null)
    {
        $path = $this->fullPath($entry);
        try {
            if (!is_null($length) && is_null($offset)) {
                $content = @file_get_contents($path, false, null, 0, $length);
            } elseif (is_null($offset)) {
                $content = @file_get_contents($path);
            } else {
                $content = @file_get_contents($path, false, null, $offset, $length);
            }
            if (false !== $content) {
                return $content;
            }
            throw new FilesException($this->lang->flCannotLoadFile($path));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function saveFile(array $entry, $content): bool
    {
        $path = $this->fullPath($entry);
        try {
            $result = @file_put_contents($path, $content);
            if (false === $result) {
                throw new FilesException($this->lang->flCannotSaveFile($path));
            }
            return true;
        } catch (Error $ex) {
            throw new FilesException($this->lang->flCannotSaveFile($path), $ex->getCode(), $ex);
        }
    }

    public function copyFile(array $source, array $dest): bool
    {
        $src = $this->fullPath($source);
        $dst = $this->fullPath($dest);
        try {
            return @copy($src, $dst);
        } catch (Error $ex) {
            throw new FilesException($this->lang->flCannotCopyFile($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function moveFile(array $source, array $dest): bool
    {
        $src = $this->fullPath($source);
        $dst = $this->fullPath($dest);
        try {
            return @rename($src, $dst);
        } catch (Error $ex) {
            throw new FilesException($this->lang->flCannotMoveFile($src, $dst), $ex->getCode(), $ex);
        }
    }

    public function deleteFile(array $entry): bool
    {
        $path = $this->fullPath($entry);
        try {
            return @unlink($path);
        } catch (Error $ex) {
            throw new FilesException($this->lang->flCannotRemoveFile($path), $ex->getCode(), $ex);
        }
    }

    protected function fullPath(array $path): string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . $this->compactName($path);
    }
}
