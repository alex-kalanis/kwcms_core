<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Extended\Config;
use kalanis\kw_files\FilesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\TLang;
use kalanis\kw_paths\PathsException;


/**
 * Class AFiles
 * Shared operations over files
 * @package kalanis\kw_images\Sources
 */
abstract class AFiles
{
    use TLang;

    /** @var CompositeAdapter */
    protected $lib = null;
    /** @var Config */
    protected $config = null;

    public function __construct(CompositeAdapter $lib, Config $config, ?IIMTranslations $lang = null)
    {
        $this->setLang($lang);
        $this->lib = $lib;
        $this->config = $config;
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function isHere(array $path): bool
    {
        return $this->lib->isFile($this->getPath($path));
    }

    /**
     * @param string[] $path
     * @return string[]
     */
    abstract public function getPath(array $path): array;

    /**
     * @param string[] $source
     * @param string[] $target
     * @param bool $overwrite
     * @param string $sourceFileNotExistsErr
     * @param string $targetFileExistsErr
     * @param string $unlinkErr
     * @param string $copyErr
     * @throws FilesException
     * @throws PathsException
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
        if (!$this->lib->isFile($source)) {
            throw new FilesException($sourceFileNotExistsErr);
        }

        if ($this->lib->isFile($target) && !$overwrite) {
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
     * @throws PathsException
     * @return bool
     */
    protected function dataOverwriteCopy(array $source, array $target, string $unlinkErrDesc, string $copyErrDesc): bool
    {
        if ($this->lib->isFile($target) && !$this->lib->deleteFile($target)) {
            throw new FilesException($unlinkErrDesc);
        }
        if ($this->lib->isFile($source) && !$this->lib->copyFile($source, $target)) {
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
     * @throws PathsException
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
        if (!$this->lib->isFile($source)) {
            throw new FilesException($sourceFileNotExistsErr);
        }

        if ($this->lib->isFile($target) && !$overwrite) {
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
     * @throws PathsException
     * @return bool
     */
    protected function dataOverwriteRename(array $source, array $target, string $unlinkErrDesc, string $copyErrDesc): bool
    {
        if ($this->lib->isFile($target) && !$this->lib->deleteFile($target)) {
            throw new FilesException($unlinkErrDesc);
        }
        if ($this->lib->isFile($source) && !$this->lib->moveFile($source, $target)) {
            throw new FilesException($copyErrDesc);
        }
        return true;
    }

    /**
     * @param string[] $source
     * @param string $unlinkErrDesc
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    protected function dataRemove(array $source, string $unlinkErrDesc): bool
    {
        if (!$this->lib->isFile($source)) {
            return true;
        }
        if (!$this->lib->deleteFile($source)) {
            throw new FilesException($unlinkErrDesc);
        }
        return true;
    }
}
