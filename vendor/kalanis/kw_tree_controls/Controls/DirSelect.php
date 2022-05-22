<?php

namespace kalanis\kw_tree_controls\Controls;


use kalanis\kw_forms\Controls;
use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\FileNode;
use kalanis\kw_tree_controls\ControlNode;


/**
 * Class DirSelect
 * @package kalanis\kw_tree_controls\Controls
 */
class DirSelect extends ATreeControl
{
    use TSimpleValue;

    protected function renderTree(?ControlNode $baseNode): string
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

    protected function getOption(ControlNode $node): string
    {
        return $node->getControl()->render();
    }

    protected function getInput(FileNode $node): Controls\AControl
    {
        $input = new Controls\SelectOption();
        $input->setEntry($this->getKey(), $node->getPath(), $node->getPath() . DIRECTORY_SEPARATOR);
        $this->inputs[] = $input;
        return $input;
    }
}
