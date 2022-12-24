<?php

namespace kalanis\kw_images\Sources;


use kalanis\kw_files\Extended\Config;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Interfaces\IProcessNodes;
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

    /** @var IProcessNodes */
    protected $libNode = null;
    /** @var IProcessFiles */
    protected $libFile = null;
    /** @var Config */
    protected $config = null;

    public function __construct(IProcessNodes $libNode, IProcessFiles $libFile, Config $config, ?IIMTranslations $lang = null)
    {
        $this->setLang($lang);
        $this->libNode = $libNode;
        $this->libFile = $libFile;
        $this->config = $config;
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @return bool
     */
    public function isHere(array $path): bool
    {
        return $this->libNode->isFile($this->getPath($path));
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
        if (!$this->libNode->isFile($source)) {
            throw new FilesException($sourceFileNotExistsErr);
        }

        if ($this->libNode->isFile($target) && !$overwrite) {
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
        if ($this->libNode->isFile($target) && !$this->libFile->deleteFile($target)) {
            throw new FilesException($unlinkErrDesc);
        }
        if ($this->libNode->isFile($source) && !$this->libFile->copyFile($source, $target)) {
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
        if (!$this->libNode->isFile($source)) {
            throw new FilesException($sourceFileNotExistsErr);
        }

        if ($this->libNode->isFile($target) && !$overwrite) {
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
        if ($this->libNode->isFile($target) && !$this->libFile->deleteFile($target)) {
            throw new FilesException($unlinkErrDesc);
        }
        if ($this->libNode->isFile($source) && !$this->libFile->moveFile($source, $target)) {
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
        if (!$this->libNode->isFile($source)) {
            return true;
        }
        if (!$this->libFile->deleteFile($source)) {
            throw new FilesException($unlinkErrDesc);
        }
        return true;
    }
}
