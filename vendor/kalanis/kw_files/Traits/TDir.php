<?php

namespace kalanis\kw_files\Traits;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessDirs;


/**
 * trait TDir
 * @package kalanis\kw_files\Processing
 */
trait TDir
{
    use TLang;

    /** @var IProcessDirs|null */
    protected $processDir = null;

    public function setProcessDir(?IProcessDirs $dirs = null): void
    {
        $this->processDir = $dirs;
    }

    /**
     * @throws FilesException
     * @return IProcessDirs
     */
    public function getProcessDir(): IProcessDirs
    {
        if (empty($this->processDir)) {
            throw new FilesException($this->getLang()->flNoProcessDirSet());
        }
        return $this->processDir;
    }
}
