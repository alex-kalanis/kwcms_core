<?php

namespace kalanis\kw_files\Traits;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessNodes;


/**
 * Trait TNode
 * @package kalanis\kw_files\Traits
 */
trait TNode
{
    use TLang;

    protected ?IProcessNodes $processNode = null;

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
            throw new FilesException($this->getFlLang()->flNoProcessNodeSet());
        }
        return $this->processNode;
    }
}
