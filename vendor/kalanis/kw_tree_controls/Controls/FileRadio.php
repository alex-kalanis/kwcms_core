<?php

namespace kalanis\kw_tree_controls\Controls;


use kalanis\kw_forms\Controls;
use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree_controls\ControlNode;


/**
 * Class FileRadio
 * @package kalanis\kw_tree_controls\Controls
 */
class FileRadio extends ATreeControl
{
    use TRadio;
    use TSubEntry;

    protected string $templateLabel = '';

    /**
     * @param ControlNode[] $nodes
     * @return string
     */
    protected function fillEntries(array $nodes): string
    {
        $list = HtmlElement::init('ul');
        foreach ($nodes as $subNode) {
            if ($subNode->getNode() && $subNode->getNode()->isDir()) {
                $entry = $this->getSubEntry($subNode);
                $entry->addChild($this->fillEntries($subNode->getSubNodes()));
                $list->addChild($entry);
            } else {
                $list->addChild($this->getEntry($subNode));
            }
        }
        return strval($list);
    }

    protected function getEntry(ControlNode $node): HtmlElement
    {
        $entry = HtmlElement::init('li', ['class' => 'file']);
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
