<?php

namespace kalanis\kw_files\Extended;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Interfaces\IProcessNodes;


/**
 * Class Processor
 * low-level work with extended dirs - which contains other params than just files and sub dirs
 */
class Processor
{
    /** @var IProcessDirs */
    protected $dirProcessor = null; # what will check content
    /** @var IProcessFiles */
    protected $fileProcessor = null; # what will check content
    /** @var IProcessNodes */
    protected $nodeProcessor = null; # what will check content
    /** @var Config */
    protected $config = null; # configuration class

    public function __construct(IProcessDirs $dirProcessor, IProcessFiles $fileProcessor, IProcessNodes $nodeProcessor, Config $config)
    {
        $this->dirProcessor = $dirProcessor;
        $this->fileProcessor = $fileProcessor;
        $this->nodeProcessor = $nodeProcessor;
        $this->config = $config;
    }

    public function getDirProcessor(): IProcessDirs
    {
        return $this->dirProcessor;
    }

    public function getFileProcessor(): IProcessFiles
    {
        return $this->fileProcessor;
    }

    public function getNodeProcessor(): IProcessNodes
    {
        return $this->nodeProcessor;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param string[] $path the path inside the web root dir
     * @param bool $makeExtra
     * @throws FilesException
     * @return bool
     */
    public function createDir(array $path, bool $makeExtra = false): bool
    {
        return $this->dirProcessor->createDir($path)
            && ( $makeExtra ? $this->makeExtended($path) : true );
    }

    /**
     * Make dir with extended properties
     * @param string[] $path
     * @throws FilesException
     * @return bool
     */
    public function makeExtended(array $path): bool
    {
        $desc = $path + [$this->config->getDescDir()];
        $thumb = $path + [$this->config->getThumbDir()];
        $descExists = $this->nodeProcessor->exists($desc);
        $thumbExists = $this->nodeProcessor->exists($thumb);
        if ($descExists && !$this->nodeProcessor->isDir($desc)) {
            return false;
        }
        if ($thumbExists && !$this->nodeProcessor->isDir($thumb)) {
            return false;
        }
        if (!$descExists) {
            $this->dirProcessor->createDir($desc);
        }
        if (!$thumbExists) {
            $this->dirProcessor->createDir($thumb);
        }
        return true;
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @return bool
     */
    public function removeExtended(array $path): bool
    {
        $desc = $path + [$this->config->getDescDir()];
        $thumb = $path + [$this->config->getThumbDir()];
        $descExists = $this->nodeProcessor->exists($desc);
        $thumbExists = $this->nodeProcessor->exists($thumb);
        if ($descExists && !$this->nodeProcessor->isDir($desc)) {
            return false;
        }
        if ($thumbExists && !$this->nodeProcessor->isDir($thumb)) {
            return false;
        }
        if ($descExists) {
            $this->dirProcessor->deleteDir($desc, true);
        }
        if ($thumbExists) {
            $this->dirProcessor->deleteDir($thumb, true);
        }
        return true;
    }
}
