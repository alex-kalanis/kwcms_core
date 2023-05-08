<?php

namespace kalanis\kw_files\Extended;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_paths\PathsException;


/**
 * Class Processor
 * low-level work with extended dirs - which contains other params than just files and sub dirs
 */
class Processor
{
    /** @var CompositeAdapter */
    protected $files = null;
    /** @var Config */
    protected $config = null;

    public function __construct(CompositeAdapter $files, Config $config)
    {
        $this->files = $files;
        $this->config = $config;
    }

    /**
     * @param string[] $path the path inside the web root dir
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function exists(array $path): bool
    {
        return $this->files->exists($path);
    }

    /**
     * @param string[] $path the path inside the web root dir
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function dirExists(array $path): bool
    {
        return $this->files->isDir($path);
    }

    /**
     * @param string[] $path the path inside the web root dir
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function fileExists(array $path): bool
    {
        return $this->files->isFile($path);
    }

    /**
     * @param string[] $path the path inside the web root dir
     * @param bool $makeExtra
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function createDir(array $path, bool $makeExtra = false): bool
    {
        return $this->files->createDir($path, true)
            && ($makeExtra ? $this->makeExtended($path) : true);
    }

    /**
     * @param string[] $path the path inside the web root dir
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function removeDir(array $path): bool
    {
        return ($this->isExtended($path) ? $this->removeExtended($path) : true) && $this->files->deleteDir($path);
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function isExtended(array $path): bool
    {
        $desc = array_merge($path, [$this->config->getDescDir()]);
        $thumb = array_merge($path, [$this->config->getThumbDir()]);
        return $this->files->exists($desc)
            && $this->files->isDir($desc)
            && $this->files->exists($thumb)
            && $this->files->isDir($thumb)
        ;
    }

    /**
     * Make dir with extended properties
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function makeExtended(array $path): bool
    {
        $desc = array_merge($path, [$this->config->getDescDir()]);
        $thumb = array_merge($path, [$this->config->getThumbDir()]);
        $descExists = $this->files->exists($desc);
        $thumbExists = $this->files->exists($thumb);
        if ($descExists && !$this->files->isDir($desc)) {
            return false;
        }
        if ($thumbExists && !$this->files->isDir($thumb)) {
            return false;
        }
        $ret = true;
        if (!$descExists) {
            $ret &= $this->files->createDir($desc, true);
        }
        if (!$thumbExists) {
            $ret &= $this->files->createDir($thumb, true);
        }
        return boolval($ret);
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function removeExtended(array $path): bool
    {
        $desc = array_merge($path, [$this->config->getDescDir()]);
        $thumb = array_merge($path, [$this->config->getThumbDir()]);
        $descExists = $this->files->exists($desc);
        $thumbExists = $this->files->exists($thumb);
        if ($descExists && !$this->files->isDir($desc)) {
            return false;
        }
        if ($thumbExists && !$this->files->isDir($thumb)) {
            return false;
        }
        $ret = true;
        if ($descExists) {
            $ret &= $this->files->deleteDir($desc, true);
        }
        if ($thumbExists) {
            $ret &= $this->files->deleteDir($thumb, true);
        }
        return boolval($ret);
    }
}
