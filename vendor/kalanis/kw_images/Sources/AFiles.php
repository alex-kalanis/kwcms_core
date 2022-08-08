<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\CompositeProcessor;
use kalanis\kw_files\Extended\Config;
use kalanis\kw_files\FilesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\TLang;


/**
 * Class AFiles
 * Shared operations over files
 * @package kalanis\kw_images\Sources
 */
abstract class AFiles
{
    use TLang;

    /** @var CompositeProcessor|null */
    protected $libProcessor = null;
    /** @var Config|null */
    protected $config = null;

    public function __construct(CompositeProcessor $libProcessor, Config $config, ?IIMTranslations $lang = null)
    {
        $this->setLang($lang);
        $this->libProcessor = $libProcessor;
        $this->config = $config;
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
     * @return bool
     */
    protected function dataCopy(
        array $source,
        array $target,
        bool $overwrite,
        string $sourceFileNotExistsErr,
        string $targetFileExistsErr,
        string $unlinkErr,
        string $copyErr
    ): bool
    {
        if (!$this->libProcessor->getNodeProcessor()->isFile($source)) {
            throw new FilesException($sourceFileNotExistsErr);
        }

        if ($this->libProcessor->getNodeProcessor()->isFile($target) && !$overwrite) {
            throw new FilesException($targetFileExistsErr);
        }

        return $this->dataOverwriteCopy( $source, $target, $unlinkErr, $copyErr);
    }

    /**
     * @param string[] $source
     * @param string[] $target
     * @param string $unlinkErrDesc
     * @param string $copyErrDesc
     * @throws FilesException
     * @return bool
     */
    protected function dataOverwriteCopy(array $source, array $target, string $unlinkErrDesc, string $copyErrDesc): bool
    {
        if ($this->libProcessor->getNodeProcessor()->isFile($target) && !$this->libProcessor->getFileProcessor()->deleteFile($target)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($unlinkErrDesc);
        }
        // @codeCoverageIgnoreEnd
        if ($this->libProcessor->getNodeProcessor()->isFile($source) && !$this->libProcessor->getFileProcessor()->copyFile($source, $target)) {
            throw new FilesException($copyErrDesc);
        }
        return true;
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
     * @return bool
     */
    protected function dataRename(
        array $source,
        array $target,
        bool $overwrite,
        string $sourceFileNotExistsErr,
        string $targetFileExistsErr,
        string $unlinkErr,
        string $copyErr
    ): bool
    {
        if (!$this->libProcessor->getNodeProcessor()->isFile($source)) {
            throw new FilesException($sourceFileNotExistsErr);
        }

        if ($this->libProcessor->getNodeProcessor()->isFile($target) && !$overwrite) {
            throw new FilesException($targetFileExistsErr);
        }

        return $this->dataOverwriteRename( $source, $target, $unlinkErr, $copyErr);
    }

    /**
     * @param string[] $source
     * @param string[] $target
     * @param string $unlinkErrDesc
     * @param string $copyErrDesc
     * @throws FilesException
     * @return bool
     */
    protected function dataOverwriteRename(array $source, array $target, string $unlinkErrDesc, string $copyErrDesc): bool
    {
        if ($this->libProcessor->getNodeProcessor()->isFile($target) && !$this->libProcessor->getFileProcessor()->deleteFile($target)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($unlinkErrDesc);
        }
        // @codeCoverageIgnoreEnd
        if ($this->libProcessor->getNodeProcessor()->isFile($source) && !$this->libProcessor->getFileProcessor()->moveFile($source, $target)) {
            throw new FilesException($copyErrDesc);
        }
        return true;
    }

    /**
     * @param string[] $source
     * @param string $unlinkErrDesc
     * @throws FilesException
     * @return bool
     */
    protected function dataRemove(array $source, string $unlinkErrDesc): bool
    {
        if (!$this->libProcessor->getNodeProcessor()->isFile($source)) {
            return true;
        }
        if ($this->libProcessor->getNodeProcessor()->isFile($source) && !$this->libProcessor->getFileProcessor()->deleteFile($source)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($unlinkErrDesc);
        }
        // @codeCoverageIgnoreEnd
        return true;
    }
}
