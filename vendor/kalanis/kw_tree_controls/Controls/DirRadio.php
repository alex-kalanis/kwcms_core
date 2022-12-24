<?php

namespace kalanis\kw_tree_controls\Controls;


use kalanis\kw_forms\Controls;
use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree_controls\ControlNode;


/**
 * Class DirRadio
 * @package kalanis\kw_tree_controls\Controls
 */
class DirRadio extends ATreeControl
{
    use TRadio;
    use TSubEntry;

    protected $templateLabel = '';

    /**
     * @param ControlNode[] $nodes
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

    protected function getEntry(ControlNode $node): HtmlElement
    {
        $entry = HtmlElement::init('li', ['class' => 'dir']);
        if ($childControl = $node->getControl()) {
            $entry->addChild($childControl);
        }
        return $entry;
    }

    protected function getInput(FileNode $node): Controls\AControl
    {
        $input = new Radio();
        $input->set($this->getKey(), $this->stringPath($node), $this->stringName($node));
        $this->inputs[] = $input;
        return $input;
    }
}
