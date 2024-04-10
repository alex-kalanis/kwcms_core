<?php

namespace kalanis\kw_files\Traits;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessDirs;


/**
 * trait TDir
 * @package kalanis\kw_files\Traits
 */
trait TDir
{
    use TLang;

    protected ?IProcessDirs $processDir = null;

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
            throw new FilesException($this->getFlLang()->flNoProcessDirSet());
        }
        return $this->processDir;
    }
}
