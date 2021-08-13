<?php

namespace kalanis\kw_tree\Controls;


use kalanis\kw_forms\Controls\Checkbox;
use kalanis\kw_forms\Interfaces\IMultiValue;
use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\FileNode;


/**
 * Class DirCheckboxes
 * @package kalanis\kw_tree\Controls
 */
class DirCheckboxes extends ATreeControl implements IMultiValue
{
    use TMultiValue;
    use TSubEntry;

    protected $templateLabel = '';
    /** @var Checkbox[] */
    protected $inputs = [];

    /**
     * @param FileNode[] $nodes
     * @param string $presetValue
     * @return string
     */
    protected function fillEntries(array $nodes, string $presetValue): string
    {
        $list = HtmlElement::init('ul');
        foreach ($nodes as $subNode) {
            $entry = $this->getEntry($subNode, $presetValue);
            $entry->addChild($this->fillEntries($subNode->getSubNodes(), $presetValue));
            $list->addChild($entry);
        }
        return strval($list);
    }

    protected function getEntry(FileNode $node, string $presetValue): HtmlElement
    {
        $entry = HtmlElement::init('li');
        $input = new Checkbox();
        $input->set($this->key, $node->getPath(), $node->getName());
        $input->setValue($node->getPath() == $presetValue ? '1' : 'none');
        $this->inputs[] = $input;
        $entry->addChild($input);
        return $entry;
    }
}
