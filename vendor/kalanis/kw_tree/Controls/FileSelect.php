<?php

namespace kalanis\kw_tree\Controls;


use kalanis\kw_forms\Controls\SelectOption;
use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\FileNode;


/**
 * Class FileSelect
 * @package kalanis\kw_tree\Controls
 */
class FileSelect extends ATreeControl
{
    protected function renderTree(?FileNode $baseNode, string $presetValue): string
    {
        if (empty($baseNode)) {
            return '';
        }
        $select = HtmlElement::init('select');
        $select->setAttribute('name', $this->key);
        $select->addChild($this->getOptionGroup($baseNode, $presetValue));
        return $select->render();
    }

    protected function getOptionGroup(FileNode $node, string $presetValue): string
    {
        if ($node->isDir()) {
            $group = HtmlElement::init('optgroup', ['label' => $node->getPath()]);
            foreach ($node->getSubNodes() as $subNode) {
                $group->addChild($this->getOptionGroup($subNode, $presetValue));
            }
            return strval($group);
        } else {
            return $this->getOption($node, $presetValue);
        }
    }

    protected function getOption(FileNode $node, string $presetValue): string
    {
        $option = new SelectOption();
        $option->setEntry($this->key, $node->getPath(), $node->getName());
        $option->setValue($presetValue);
        $this->inputs[] = $option;
        return $option->render();
    }
}
