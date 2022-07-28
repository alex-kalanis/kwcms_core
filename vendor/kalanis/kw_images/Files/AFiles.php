<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_files\Extended\Processor;
use kalanis\kw_files\FilesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\TLang;


/**
 * Class AFiles
 * Shared operations over files
 * @package kalanis\kw_images\Files
 */
abstract class AFiles
{
    use TLang;

    /** @var Processor|null */
    protected $libProcessor = null;

    public function __construct(Processor $libProcessor, ?IIMTranslations $lang = null)
    {
        $this->setLang($lang);
        $this->libProcessor = $libProcessor;
    }

    public function getProcessor(): Processor
    {
        return $this->libProcessor;
    }

    /**
     * @param string $path
     * @throws FilesException
     * @return bool
     */
    public function isHere(string $path): bool
    {
        return $this->libProcessor->getNodeProcessor()->isFile($this->getPath($path));
    }

    abstract public function getPath(string $path): array;

    /**
     * @param string[] $source
     * @param string[] $target
     * @param bool $overwrite
     * @param string $sourceFileNotExistsErr
     * @param string $targetFileExistsErr
     * @param string $unlinkErr
     * @param string $copyErr
     * @throws FilesException
     */
    protected function dataCopy(
        array $source, array $target, bool $overwrite, string $sourceFileNotExistsErr, string $targetFileExistsErr, string $unlinkErr, string $copyErr
    ): void
    {
        if (!$this->libProcessor->getNodeProcessor()->isFile($source)) {
            throw new FilesException($sourceFileNotExistsErr);
        }

        if ($this->libProcessor->getNodeProcessor()->isFile($target) && !$overwrite) {
            throw new FilesException($targetFileExistsErr);
        }

        $this->dataOverwriteCopy( $source, $target, $unlinkErr, $copyErr);
    }

    /**
     * @param string[] $source
     * @param string[] $target
     * @param string $unlinkErrDesc
     * @param string $copyErrDesc
     * @throws FilesException
     */
    protected function dataOverwriteCopy(array $source, array $target, string $unlinkErrDesc, string $copyErrDesc): void
    {
        if ($this->libProcessor->getNodeProcessor()->isFile($target) && !$this->libProcessor->getFileProcessor()->deleteFile($target)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($unlinkErrDesc);
        }
        // @codeCoverageIgnoreEnd
        if ($this->libProcessor->getNodeProcessor()->isFile($source) && !$this->libProcessor->getFileProcessor()->copyFile($source, $target)) {
            throw new FilesException($copyErrDesc);
        }
    }

    /**
     * @param string[] $source
     * @param string[] $target
     * @param bool $overwrite
     * @param string $sourceFileNotExistsErr
     * @param string $targetFileExistsErr
     * @param string $unlinkErr
     * @param string $copyErr
     * @throws FilesException
     */
    protected function dataRename(
        array $source, array $target, bool $overwrite, string $sourceFileNotExistsErr, string $targetFileExistsErr, string $unlinkErr, string $copyErr
    ): void
    {
        if (!$this->libProcessor->getNodeProcessor()->isFile($source)) {
            throw new FilesException($sourceFileNotExistsErr);
        }

        if ($this->libProcessor->getNodeProcessor()->isFile($target) && !$overwrite) {
            throw new FilesException($targetFileExistsErr);
        }

        $this->dataOverwriteRename( $source, $target, $unlinkErr, $copyErr);
    }

    /**
     * @param string[] $source
     * @param string[] $target
     * @param string $unlinkErrDesc
     * @param string $copyErrDesc
     * @throws FilesException
     */
    protected function dataOverwriteRename(array $source, array $target, string $unlinkErrDesc, string $copyErrDesc): void
    {
        if ($this->libProcessor->getNodeProcessor()->isFile($target) && !$this->libProcessor->getFileProcessor()->deleteFile($target)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($unlinkErrDesc);
        }
        // @codeCoverageIgnoreEnd
        if ($this->libProcessor->getNodeProcessor()->isFile($source) && !$this->libProcessor->getFileProcessor()->moveFile($source, $target)) {
            throw new FilesException($copyErrDesc);
        }
    }

    /**
     * @param string[] $source
     * @param string $unlinkErrDesc
     * @throws FilesException
     */
    protected function dataRemove(array $source, string $unlinkErrDesc): void
    {
        if (!$this->libProcessor->getNodeProcessor()->isFile($source)) {
            return;
        }
        if ($this->libProcessor->getNodeProcessor()->isFile($source) && !$this->libProcessor->getFileProcessor()->deleteFile($source)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($unlinkErrDesc);
        }
        // @codeCoverageIgnoreEnd
    }
}
