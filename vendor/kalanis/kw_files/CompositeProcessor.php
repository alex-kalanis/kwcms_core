<?php

namespace kalanis\kw_files;


use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Interfaces\IProcessNodes;


/**
 * Class CompositeProcessor
 * @package kalanis\kw_files
 * Access all storage processors in one object
 */
class CompositeProcessor
{
    /** @var IProcessDirs */
    protected $dirProcessor = null;
    /** @var IProcessFiles */
    protected $fileProcessor = null;
    /** @var IProcessNodes */
    protected $nodeProcessor = null;

    public function setData(IProcessDirs $dirProcessor, IProcessFiles $fileProcessor, IProcessNodes $nodeProcessor): self
    {
        $this->dirProcessor = $dirProcessor;
        $this->fileProcessor = $fileProcessor;
        $this->nodeProcessor = $nodeProcessor;
        return $this;
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
}
