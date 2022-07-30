<?php

namespace kalanis\kw_files\Extended;


use kalanis\kw_files\CompositeProcessor;
use kalanis\kw_files\FilesException;


/**
 * Class Processor
 * low-level work with extended dirs - which contains other params than just files and sub dirs
 */
class Processor
{
    /** @var CompositeProcessor */
    protected $files = null;
    /** @var Config */
    protected $config = null;

    public function __construct(CompositeProcessor $files, Config $config)
    {
        $this->files = $files;
        $this->config = $config;
    }

    /**
     * @param string[] $path the path inside the web root dir
     * @param bool $makeExtra
     * @throws FilesException
     * @return bool
     */
    public function createDir(array $path, bool $makeExtra = false): bool
    {
        return $this->files->getDirProcessor()->createDir($path)
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
        $descExists = $this->files->getNodeProcessor()->exists($desc);
        $thumbExists = $this->files->getNodeProcessor()->exists($thumb);
        if ($descExists && !$this->files->getNodeProcessor()->isDir($desc)) {
            return false;
        }
        if ($thumbExists && !$this->files->getNodeProcessor()->isDir($thumb)) {
            return false;
        }
        if (!$descExists) {
            $this->files->getDirProcessor()->createDir($desc);
        }
        if (!$thumbExists) {
            $this->files->getDirProcessor()->createDir($thumb);
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
        $descExists = $this->files->getNodeProcessor()->exists($desc);
        $thumbExists = $this->files->getNodeProcessor()->exists($thumb);
        if ($descExists && !$this->files->getNodeProcessor()->isDir($desc)) {
            return false;
        }
        if ($thumbExists && !$this->files->getNodeProcessor()->isDir($thumb)) {
            return false;
        }
        if ($descExists) {
            $this->files->getDirProcessor()->deleteDir($desc, true);
        }
        if ($thumbExists) {
            $this->files->getDirProcessor()->deleteDir($thumb, true);
        }
        return true;
    }
}
