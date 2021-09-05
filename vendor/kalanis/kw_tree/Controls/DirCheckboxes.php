<?php

namespace kalanis\kw_tree\Controls;


use kalanis\kw_forms\Controls;
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

    /**
     * @param FileNode[] $nodes
     * @return string
     */
    protected function fillEntries(array $nodes): string
    {
        $list = HtmlElement::init('ul');
        foreach ($nodes as $subNode) {
            $entry = $this->getEntry($subNode);
            $entry->addChild($this->fillEntries($subNode->getSubNodes()));
            $list->addChild($entry);
        }
        return strval($list);
    }

    protected function getEntry(FileNode $node): HtmlElement
    {
        $entry = HtmlElement::init('li', ['class' => 'dir']);
        $entry->addChild($node->getControl());
        return $entry;
    }

    protected function getInput(FileNode $node): Controls\AControl
    {
        $input = new Controls\Checkbox();
        $input->set($this->getKey(), $node->getPath(), $node->getName());
        $this->inputs[] = $input;
        return $input;
    }
}
