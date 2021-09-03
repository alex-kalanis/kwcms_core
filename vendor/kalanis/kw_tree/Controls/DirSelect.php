<?php

namespace kalanis\kw_tree\Controls;


use kalanis\kw_forms\Controls;
use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\FileNode;


/**
 * Class DirSelect
 * @package kalanis\kw_tree\Controls
 */
class DirSelect extends ATreeControl
{
    use TSimpleValue;

    protected function renderTree(?FileNode $baseNode): string
    {
        if (empty($baseNode)) {
            return '';
        }
        $select = HtmlElement::init('select');
        $select->setAttribute('name', $this->key);
        $select->addChild($this->fillOptions([$baseNode]));
        return $select->render();
    }

    protected function fillOptions(array $nodes): string
    {
        $result = [];
        foreach ($nodes as $subNode) {
            $result[] = $this->getOption($subNode)
                . $this->fillOptions($subNode->getSubNodes());
        }
        return implode('', $result);
    }

    protected function getOption(FileNode $node): string
    {
        return $node->getControl()->render();
    }

    protected function getInput(FileNode $node): Controls\AControl
    {
        $input = new Controls\SelectOption();
        $input->setEntry($this->getKey(), $node->getPath(), $node->getPath());
        $this->inputs[] = $input;
        return $input;
    }
}
