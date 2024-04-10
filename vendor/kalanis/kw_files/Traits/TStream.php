<?php

namespace kalanis\kw_files\Traits;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessFileStreams;


/**
 * Trait TStream
 * @package kalanis\kw_files\Traits
 */
trait TStream
{
    use TLang;

    protected ?IProcessFileStreams $processStream = null;

    public function setProcessStream(?IProcessFileStreams $dirs = null): void
    {
        $this->processStream = $dirs;
    }

    /**
     * @throws FilesException
     * @return IProcessFileStreams
     */
    public function getProcessStream(): IProcessFileStreams
    {
        if (empty($this->processStream)) {
            throw new FilesException($this->getFlLang()->flNoProcessStreamSet());
        }
        return $this->processStream;
    }
}
