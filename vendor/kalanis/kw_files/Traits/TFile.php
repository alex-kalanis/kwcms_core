<?php

namespace kalanis\kw_files\Traits;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessFiles;


/**
 * trait TFile
 * @package kalanis\kw_files\Traits
 */
trait TFile
{
    use TLang;

    protected ?IProcessFiles $processFile = null;

    public function setProcessFile(?IProcessFiles $dirs = null): void
    {
        $this->processFile = $dirs;
    }

    /**
     * @throws FilesException
     * @return IProcessFiles
     */
    public function getProcessFile(): IProcessFiles
    {
        if (empty($this->processFile)) {
            throw new FilesException($this->getFlLang()->flNoProcessFileSet());
        }
        return $this->processFile;
    }
}
