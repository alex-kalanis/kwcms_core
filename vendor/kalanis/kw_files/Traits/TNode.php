<?php

namespace kalanis\kw_files\Traits;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessNodes;


/**
 * Trait TNode
 * @package kalanis\kw_files\Processing
 */
trait TNode
{
    use TLang;

    /** @var IProcessNodes|null */
    protected $processNode = null;

    public function setProcessNode(?IProcessNodes $dirs = null): void
    {
        $this->processNode = $dirs;
    }

    /**
     * @throws FilesException
     * @return IProcessNodes
     */
    public function getProcessNode(): IProcessNodes
    {
        if (empty($this->processNode)) {
            throw new FilesException($this->getLang()->flNoProcessNodeSet());
        }
        return $this->processNode;
    }
}
