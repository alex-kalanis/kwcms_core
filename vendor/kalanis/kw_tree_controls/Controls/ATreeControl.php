<?php

namespace kalanis\kw_tree_controls\Controls;


use kalanis\kw_forms\Controls;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree_controls\ControlNode;


/**
 * Class ATreeControl
 * @package kalanis\kw_tree_controls\Controls
 */
abstract class ATreeControl extends Controls\AControl
{
    protected $templateInput = '%1$s'; // by our own!
    /** @var ControlNode|null */
    protected $tree = null;
    /** @var Controls\AControl[]|Controls\Checkbox[]|Controls\Radio[] */
    protected $inputs = [];

    public function set(string $key, string $value = '', string $label = '', ?FileNode $tree = null): self
    {
        $this->setEntry($key, $value, $label);
        $this->tree = $this->fillTreeControl($tree);
        $this->setValue($value);
        return $this;
    }

    public function renderInput($attributes = null): string
    {
        $this->addAttributes($attributes);
        return $this->wrapIt(sprintf($this->templateInput, $this->renderTree($this->tree)), $this->wrappersInput);
    }

    protected function fillTreeControl(?FileNode $baseNode): ?ControlNode
    {
        if (!$baseNode) {
            return null;
        }
        $node = $this->getControlNode();
        $node->setControl($this->getInput($baseNode));
        $node->setNode($baseNode);
        foreach ($baseNode->getSubNodes() as $subNode) {
            if ($subControl = $this->fillTreeControl($subNode)) {
                $node->addSubNode($subControl);
            }
        }
        return $node;
    }

    protected function getControlNode(): ControlNode
    {
        return new ControlNode();
    }

    abstract protected function getInput(FileNode $node): Controls\AControl;

    abstract protected function renderTree(?ControlNode $baseNode): string;

    protected function stringName(?FileNode $node): string
    {
        if (is_null($node)) {
            return '';
        }
        $path = $node->getPath();
        $last = end($path);
        return (false !== $last) ? strval($last) : IPaths::SPLITTER_SLASH;
    }

    protected function stringPath(?FileNode $node): string
    {
        return is_null($node)
            ? IPaths::SPLITTER_SLASH
            : implode(IPaths::SPLITTER_SLASH, $node->getPath());
    }
}
