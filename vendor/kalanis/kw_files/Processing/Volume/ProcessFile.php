<?php

namespace kalanis\kw_files\Processing\Volume;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Processing\TPath;
use kalanis\kw_files\Traits\TLang;
use kalanis\kw_files\Traits\TStreamToPos;
use kalanis\kw_files\Traits\TToStream;
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
    use TStreamToPos;
    use TToStream;

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

    public function saveFile(array $entry, $content, ?int $offset = null): bool
    {
        $path = $this->fullPath($entry);
        try {
            if (is_null($offset)) {  // rewrite all
                $this->writeStream($path, $this->toStream($path, $content));
            } else { // append from position
                if (file_exists($path)) {
                    $handler = @fopen($path, 'rb');
                    if (false === $handler) {
                        // @codeCoverageIgnoreStart
                        throw new FilesException($this->getLang()->flCannotOpenFile($path));
                    }
                    // @codeCoverageIgnoreEnd
                    // must be extra - need that original file handler
                    $result = $this->addStreamToPosition(
                        $handler, // original content
                        $this->toStream(
                            $path,
                            $content
                        ),
                        $offset
                    );
                    /** @scrutinizer ignore-unhandled */@fclose($handler);
                    $this->writeStream($path, $result);
                } else {
                    $handler = @fopen('php://memory', 'rb+');
                    if (false === $handler) {
                        // @codeCoverageIgnoreStart
                        throw new FilesException($this->getLang()->flCannotOpenFile($path));
                    }
                    // @codeCoverageIgnoreEnd
                    $this->writeStream(
                        $path,
                        $this->addStreamToPosition(
                            $handler, // no original content
                            $this->toStream(
                                $path,
                                $content
                            ),
                            $offset
                        )
                    );
                }
            }
            return true;
        } catch (Throwable $ex) {
            // @codeCoverageIgnoreStart
            throw new FilesException($this->getLang()->flCannotSaveFile($path), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     * @param resource $content
     * @throws FilesException
     */
    protected function writeStream(string $path, $content): void
    {
        if (false === file_put_contents($path, $content)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($this->getLang()->flCannotWriteFile($path));
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
