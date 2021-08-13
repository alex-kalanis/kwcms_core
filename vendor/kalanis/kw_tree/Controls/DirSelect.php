<?php

namespace kalanis\kw_tree\Controls;


use kalanis\kw_forms\Controls\SelectOption;
use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\FileNode;


/**
 * Class DirSelect
 * @package kalanis\kw_tree\Controls
 */
class DirSelect extends ATreeControl
{
    protected function renderTree(?FileNode $baseNode, string $presetValue): string
    {
        if (empty($baseNode)) {
            return '';
        }
        $select = HtmlElement::init('select');
        $select->setAttribute('name', $this->key);
        $select->addChild($this->fillOptions([$baseNode], $presetValue));
        return $select->render();
    }

    protected function fillOptions(array $nodes, string $presetValue): string
    {
        $result = [];
        foreach ($nodes as $subNode) {
            $result[] = $this->getOption($subNode, $presetValue)
                . $this->fillOptions($subNode->getSubNodes(), $presetValue);
        }
        return implode('', $result);
    }

    protected function getOption(FileNode $node, string $presetValue): string
    {
        $option = new SelectOption();
        $option->setEntry($this->key, $node->getPath(), $node->getPath());
        $option->setValue($presetValue);
        $this->inputs[] = $option;
        return $option->render();
    }
}
