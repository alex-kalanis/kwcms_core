<?php

namespace kalanis\kw_files\Extended;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces;


/**
 * Class Processor
 * low-level work with extended dirs - which contains other params than just files and sub dirs
 */
class Processor
{
    /** @var Interfaces\IProcessDirs */
    protected $dirs = null;
    /** @var Interfaces\IProcessNodes */
    protected $nodes = null;
    /** @var Config */
    protected $config = null;

    public function __construct(Interfaces\IProcessDirs $dirs, Interfaces\IProcessNodes $nodes, Config $config)
    {
        $this->dirs = $dirs;
        $this->nodes = $nodes;
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
        return $this->dirs->createDir($path, true)
            && ($makeExtra ? $this->makeExtended($path) : true);
    }

    /**
     * @param string[] $path the path inside the web root dir
     * @throws FilesException
     * @return bool
     */
    public function removeDir(array $path): bool
    {
        return ($this->isExtended($path) ? $this->removeExtended($path) : true) && $this->dirs->deleteDir($path);
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @return bool
     */
    public function isExtended(array $path): bool
    {
        $desc = array_merge($path, [$this->config->getDescDir()]);
        $thumb = array_merge($path, [$this->config->getThumbDir()]);
        return $this->nodes->exists($desc)
            && $this->nodes->isDir($desc)
            && $this->nodes->exists($thumb)
            && $this->nodes->isDir($thumb)
        ;
    }

    /**
     * Make dir with extended properties
     * @param string[] $path
     * @throws FilesException
     * @return bool
     */
    public function makeExtended(array $path): bool
    {
        $desc = array_merge($path, [$this->config->getDescDir()]);
        $thumb = array_merge($path, [$this->config->getThumbDir()]);
        $descExists = $this->nodes->exists($desc);
        $thumbExists = $this->nodes->exists($thumb);
        if ($descExists && !$this->nodes->isDir($desc)) {
            return false;
        }
        if ($thumbExists && !$this->nodes->isDir($thumb)) {
            return false;
        }
        $ret = true;
        if (!$descExists) {
            $ret &= $this->dirs->createDir($desc, true);
        }
        if (!$thumbExists) {
            $ret &= $this->dirs->createDir($thumb, true);
        }
        return boolval($ret);
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @return bool
     */
    public function removeExtended(array $path): bool
    {
        $desc = array_merge($path, [$this->config->getDescDir()]);
        $thumb = array_merge($path, [$this->config->getThumbDir()]);
        $descExists = $this->nodes->exists($desc);
        $thumbExists = $this->nodes->exists($thumb);
        if ($descExists && !$this->nodes->isDir($desc)) {
            return false;
        }
        if ($thumbExists && !$this->nodes->isDir($thumb)) {
            return false;
        }
        $ret = true;
        if ($descExists) {
            $ret &= $this->dirs->deleteDir($desc, true);
        }
        if ($thumbExists) {
            $ret &= $this->dirs->deleteDir($thumb, true);
        }
        return boolval($ret);
    }
}
