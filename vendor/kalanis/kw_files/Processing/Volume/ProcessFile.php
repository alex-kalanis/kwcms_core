<?php

namespace kalanis\kw_files\Processing\Volume;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces;
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
class ProcessFile implements Interfaces\IProcessFiles, Interfaces\IProcessFileStreams
{
    use TLang;
    use TPath;
    use TPathTransform;

    public function __construct(string $path = '', ?Interfaces\IFLTranslations $lang = null)
    {
        $this->setPath($path);
        $this->setFlLang($lang);
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null): string
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
                return strval($content);
            }
            throw new FilesException($this->getFlLang()->flCannotLoadFile($path));
        } catch (Throwable $ex) {
            // @codeCoverageIgnoreStart
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function readFileStream(array $entry)
    {
        $path = $this->fullPath($entry);
        try {
            $handle = @fopen($path, 'rb');
            if (false !== $handle) {
                return $handle;
            }
            throw new FilesException($this->getFlLang()->flCannotLoadFile($path));
        } catch (Throwable $ex) {
            // @codeCoverageIgnoreStart
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function saveFile(array $entry, string $content, ?int $offset = null, int $mode = 0): bool
    {
        $this->checkSupportedModes($mode);
        $path = $this->fullPath($entry);
        try {
            if (FILE_APPEND == $mode) {
                $handler = @fopen($path, 'ab');
            } else {
                $handler = @fopen($path, 'wb');
            }
            if (false === $handler) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getFlLang()->flCannotOpenFile($path));
            }
            // @codeCoverageIgnoreEnd
            if (!is_null($offset)) {
                if (0 != @fseek($handler, $offset)) {
                    // @codeCoverageIgnoreStart
                    throw new FilesException($this->getFlLang()->flCannotSeekFile($path));
                }
                // @codeCoverageIgnoreEnd
            } else {
                if (!@rewind($handler)) {
                    // @codeCoverageIgnoreStart
                    throw new FilesException($this->getFlLang()->flCannotSeekFile($path));
                }
                // @codeCoverageIgnoreEnd
            }
            if (false === @fwrite($handler, $content)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getFlLang()->flCannotWriteFile($path));
            }
            // @codeCoverageIgnoreEnd
            return @fclose($handler);
        } catch (Throwable $ex) {
            // @codeCoverageIgnoreStart
            throw new FilesException($this->getFlLang()->flCannotSaveFile($path), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function saveFileStream(array $entry, $content, int $mode = 0): bool
    {
        $this->checkSupportedModes($mode);
        $path = $this->fullPath($entry);
        try {
            if (FILE_APPEND == $mode) { // append to the end
                $handler = @fopen($path, 'ab');
            } else { // rewrite all from offset
                $handler = @fopen($path, 'wb');
            }
            if (false === $handler) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getFlLang()->flCannotOpenFile($path));
            }
            // @codeCoverageIgnoreEnd
            if (false === @stream_copy_to_stream($content, $handler)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getFlLang()->flCannotWriteFile($path));
            }
            // @codeCoverageIgnoreEnd
            return @fclose($handler);
        } catch (Throwable $ex) {
            // @codeCoverageIgnoreStart
            throw new FilesException($this->getFlLang()->flCannotSaveFile($path), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param int<0, max> $mode
     * @throws FilesException
     */
    protected function checkSupportedModes(int $mode): void
    {
        if (!in_array($mode, [0, FILE_APPEND])) {
            throw new FilesException($this->getFlLang()->flBadMode($mode));
        }
    }

    public function copyFile(array $source, array $dest): bool
    {
        $src = $this->fullPath($source);
        $dst = $this->fullPath($dest);
        try {
            return @copy($src, $dst);
            // @codeCoverageIgnoreStart
        } catch (Throwable $ex) {
            throw new FilesException($this->getFlLang()->flCannotCopyFile($src, $dst), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function copyFileStream(array $source, array $dest): bool
    {
        $stream = $this->readFileStream($source);
        if (!@rewind($stream)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($this->getFlLang()->flCannotSeekFile($this->fullPath($source)));
        }
        // @codeCoverageIgnoreEnd
        return $this->saveFileStream($dest, $stream);
    }

    public function moveFile(array $source, array $dest): bool
    {
        $src = $this->fullPath($source);
        $dst = $this->fullPath($dest);
        try {
            return @rename($src, $dst);
            // @codeCoverageIgnoreStart
        } catch (Throwable $ex) {
            throw new FilesException($this->getFlLang()->flCannotMoveFile($src, $dst), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function moveFileStream(array $source, array $dest): bool
    {
        $r1 = $this->copyFileStream($source, $dest);
        $r2 = $this->deleteFile($source);
        return $r1 && $r2;
    }

    public function deleteFile(array $entry): bool
    {
        $path = $this->fullPath($entry);
        try {
            return @unlink($path);
            // @codeCoverageIgnoreStart
        } catch (Throwable $ex) {
            throw new FilesException($this->getFlLang()->flCannotRemoveFile($path), $ex->getCode(), $ex);
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
        return $this->getFlLang()->flNoDirectoryDelimiterSet();
    }
}
