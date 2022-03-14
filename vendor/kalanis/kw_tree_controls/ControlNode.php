<?php

namespace kalanis\kw_tree_controls;


use kalanis\kw_forms\Controls\AControl;
use kalanis\kw_tree\FileNode;


/**
 * Class ControlNode
 * @package kalanis\kw_tree_control
 * File in directory (could be directory too)
 * Join FileNode and its control
 */
class ControlNode
{
    /** @var FileNode|null */
    protected $node = null;
    /** @var AControl|null */
    protected $control = null;
    /** @var ControlNode[] */
    protected $subNodes = [];

    public function setNode(?FileNode $node = null): self
    {
        $this->node = $node;
        return $this;
    }

    public function getNode(): ?FileNode
    {
        return $this->node;
    }

    public function setControl(?AControl $control = null): self
    {
        $this->control = $control;
        return $this;
    }

    public function getControl(): ?AControl
    {
        return $this->control;
    }

    public function addSubNode(ControlNode $node): self
    {
        $this->subNodes[] = $node;
        return $this;
    }

    /**
     * @return ControlNode[]
     */
    public function getSubNodes(): array
    {
        return $this->subNodes;
    }
}
