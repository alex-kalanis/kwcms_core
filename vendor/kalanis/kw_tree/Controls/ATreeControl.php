<?php

namespace kalanis\kw_tree\Controls;


use kalanis\kw_forms\Controls;
use kalanis\kw_tree\FileNode;


/**
 * Class ATreeControl
 * @package kalanis\kw_tree\Controls
 */
abstract class ATreeControl extends Controls\AControl
{
    protected $templateInput = '%1$s'; // by our own!
    protected $tree = null;
    /** @var Controls\AControl[]|Controls\Checkbox[]|Controls\Radio[] */
    protected $inputs = [];

    public function set(string $key, string $value = '', string $label = '', ?FileNode $tree = null)
    {
        $this->setEntry($key, $value, $label);
        $this->tree = $tree;
        $this->fillTreeControl($this->tree);
        return $this;
    }

    public function renderInput($attributes = null): string
    {
        $this->addAttributes($attributes);
        return $this->wrapIt(sprintf($this->templateInput, $this->renderTree($this->tree)), $this->wrappersInput);
    }

    protected function fillTreeControl(?FileNode $baseNode): void
    {
        if (!$baseNode) {
            return;
        }
        $baseNode->setControl($this->getInput($baseNode));
        foreach ($baseNode->getSubNodes() as $subNode) {
            $this->fillTreeControl($subNode);
        }
    }

    abstract protected function getInput(FileNode $node): Controls\AControl;

    abstract protected function renderTree(?FileNode $baseNode): string;
}
