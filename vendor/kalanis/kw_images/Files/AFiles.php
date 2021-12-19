<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_extras\ExtendDir;
use kalanis\kw_extras\ExtrasException;
use kalanis\kw_images\ImagesException;


/**
 * Class AFiles
 * Shared operations over files
 * @package kalanis\kw_images\Files
 */
abstract class AFiles
{
    /** @var ExtendDir|null */
    protected $libExtendDir = null;

    public function __construct(ExtendDir $libExtendDir)
    {
        $this->libExtendDir = $libExtendDir;
    }

    public function getExtendDir(): ExtendDir
    {
        return $this->libExtendDir;
    }

    abstract public function getPath(string $path): string;

    /**
     * @param string $path
     * @throws ExtrasException
     */
    protected function checkWritable(string $path): void
    {
        $this->libExtendDir->isWritable($path);
    }

    /**
     * @param string $source
     * @param string $target
     * @param bool $overwrite
     * @param string $sourceFileNotExistsErr
     * @param string $targetFileExistsErr
     * @param string $unlinkErr
     * @param string $copyErr
     * @throws ImagesException
     */
    protected function dataCopy(
        string $source, string $target, bool $overwrite, string $sourceFileNotExistsErr, string $targetFileExistsErr, string $unlinkErr, string $copyErr
    ): void
    {
        if (!$this->libExtendDir->isFile($source)) {
            throw new ImagesException($sourceFileNotExistsErr);
        }

        if ($this->libExtendDir->isFile($target) && !$overwrite) {
            throw new ImagesException($targetFileExistsErr);
        }

        $this->dataOverwriteCopy( $source, $target, $unlinkErr, $copyErr);
    }

    /**
     * @param string $source
     * @param string $target
     * @param string $unlinkErrDesc
     * @param string $copyErrDesc
     * @throws ImagesException
     */
    protected function dataOverwriteCopy(string $source, string $target, string $unlinkErrDesc, string $copyErrDesc): void
    {
        if ($this->libExtendDir->isFile($target) && !unlink($target)) {
            throw new ImagesException($unlinkErrDesc);
        }
        if ($this->libExtendDir->isFile($source) && !copy($source, $target)) {
            throw new ImagesException($copyErrDesc);
        }
    }

    /**
     * @param string $source
     * @param string $target
     * @param bool $overwrite
     * @param string $sourceFileNotExistsErr
     * @param string $targetFileExistsErr
     * @param string $unlinkErr
     * @param string $copyErr
     * @throws ImagesException
     */
    protected function dataRename(
        string $source, string $target, bool $overwrite, string $sourceFileNotExistsErr, string $targetFileExistsErr, string $unlinkErr, string $copyErr
    ): void
    {
        if (!$this->libExtendDir->isFile($source)) {
            throw new ImagesException($sourceFileNotExistsErr);
        }

        if ($this->libExtendDir->isFile($target) && !$overwrite) {
            throw new ImagesException($targetFileExistsErr);
        }

        $this->dataOverwriteRename( $source, $target, $unlinkErr, $copyErr);
    }

    /**
     * @param string $source
     * @param string $target
     * @param string $unlinkErrDesc
     * @param string $copyErrDesc
     * @throws ImagesException
     */
    protected function dataOverwriteRename(string $source, string $target, string $unlinkErrDesc, string $copyErrDesc): void
    {
        if ($this->libExtendDir->isFile($target) && !unlink($target)) {
            throw new ImagesException($unlinkErrDesc);
        }
        if ($this->libExtendDir->isFile($source) && !rename($source, $target)) {
            throw new ImagesException($copyErrDesc);
        }
    }

    /**
     * @param string $source
     * @param string $unlinkErrDesc
     * @throws ImagesException
     */
    protected function dataRemove(string $source, string $unlinkErrDesc): void
    {
        if (!$this->libExtendDir->isFile($source)) {
            return;
        }
        if ($this->libExtendDir->isFile($source) && !unlink($source)) {
            throw new ImagesException($unlinkErrDesc);
        }
    }
}
