<?php

namespace kalanis\kw_tree\Controls;


use kalanis\kw_forms\Controls;
use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\FileNode;


/**
 * Class DirRadio
 * @package kalanis\kw_tree\Controls
 */
class DirRadio extends ATreeControl
{
    use TRadio;
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
        $entry = HtmlElement::init('li');
        $entry->addChild($node->getControl());
        return $entry;
    }

    protected function getInput(FileNode $node): Controls\AControl
    {
        $input = new Radio();
        $input->set($this->getKey(), $node->getPath(), $node->getName());
        $this->inputs[] = $input;
        return $input;
    }
}
