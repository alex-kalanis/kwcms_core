<?php

namespace kalanis\kw_tree\Controls;


use kalanis\kw_forms\Controls\AControl;
use kalanis\kw_forms\Controls\SelectOption;
use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\FileNode;


/**
 * Class FileSelect
 * @package kalanis\kw_tree\Controls
 */
class FileSelect extends ATreeControl
{
    use TSimpleValue;

    protected function renderTree(?FileNode $baseNode): string
    {
        if (empty($baseNode)) {
            return '';
        }
        $select = HtmlElement::init('select');
        $select->setAttribute('name', $this->key);
        $select->addChild($this->getOptionGroup($baseNode));
        return $select->render();
    }

    protected function getOptionGroup(FileNode $node): string
    {
        if ($node->isDir()) {
            $group = HtmlElement::init('optgroup', ['label' => $node->getPath()]);
            foreach ($node->getSubNodes() as $subNode) {
                $group->addChild($this->getOptionGroup($subNode));
            }
            return strval($group);
        } else {
            return $this->getOption($node);
        }
    }

    protected function getOption(FileNode $node): string
    {
        return $node->getControl()->render();
    }

    protected function getInput(FileNode $node): AControl
    {
        $input = new SelectOption();
        $input->setEntry($this->getKey(), $node->getPath(), $node->getName());
        $this->inputs[] = $input;
        return $input;
    }
}
