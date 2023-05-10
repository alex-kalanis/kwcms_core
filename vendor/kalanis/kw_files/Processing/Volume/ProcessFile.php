<?php

namespace kalanis\kw_files\Processing\Volume;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Processing\TPath;
use kalanis\kw_files\Traits\TLang;
use kalanis\kw_paths\Extras\TPathTransform;
use kalanis\kw_paths\PathsException;
use Throwable;


/**
 * Class ProcessFile
 * @package kalanis\kw_files\Processing\Volume
 * Process files in many ways
 */
class ProcessFile implements IProcessFiles
{
    use TLang;
    use TPath;
    use TPathTransform;

    public function __construct(string $path = '', ?IFLTranslations $lang = null)
    {
        $this->setPath($path);
        $this->setLang($lang);
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null)
    {
        $path = $this->fullPath($entry);
        try {
            if (!is_null($length)) {
                $content = @file_get_contents($path, false, null, intval($offset), $length);
            } elseif (!is_null($offset)) {
                $content = @file_get_contents($path, false, null, $offset);
            } else {
                $content = @file_get_contents($path);
            }
            if (false !== $content) {
                return $content;
            }
            throw new FilesException($this->getLang()->flCannotLoadFile($path));
        } catch (Throwable $ex) {
            // @codeCoverageIgnoreStart
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function saveFile(array $entry, $content): bool
    {
        $path = $this->fullPath($entry);
        try {
            $result = @file_put_contents($path, $content);
            if (false === $result) {
                throw new FilesException($this->getLang()->flCannotSaveFile($path));
            }
            return true;
        } catch (Throwable $ex) {
            // @codeCoverageIgnoreStart
            throw new FilesException($this->getLang()->flCannotSaveFile($path), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function copyFile(array $source, array $dest): bool
    {
        $src = $this->fullPath($source);
        $dst = $this->fullPath($dest);
        try {
            return @copy($src, $dst);
            // @codeCoverageIgnoreStart
        } catch (Throwable $ex) {
            throw new FilesException($this->getLang()->flCannotCopyFile($src, $dst), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function moveFile(array $source, array $dest): bool
    {
        $src = $this->fullPath($source);
        $dst = $this->fullPath($dest);
        try {
            return @rename($src, $dst);
            // @codeCoverageIgnoreStart
        } catch (Throwable $ex) {
            throw new FilesException($this->getLang()->flCannotMoveFile($src, $dst), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function deleteFile(array $entry): bool
    {
        $path = $this->fullPath($entry);
        try {
            return @unlink($path);
            // @codeCoverageIgnoreStart
        } catch (Throwable $ex) {
            throw new FilesException($this->getLang()->flCannotRemoveFile($path), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param array<string> $path
     * @throws PathsException
     * @return string
     */
    protected function fullPath(array $path): string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . $this->compactName($path);
    }

    /**
     * @return string
     * @codeCoverageIgnore only when path fails
     */
    protected function noDirectoryDelimiterSet(): string
    {
        return $this->getLang()->flNoDirectoryDelimiterSet();
    }
}
