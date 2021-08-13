<?php

namespace kalanis\kw_tree\Controls;


use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\FileNode;


/**
 * Class FileRadio
 * @package kalanis\kw_tree\Controls
 */
class FileRadio extends ATreeControl
{
    use TRadio;
    use TSubEntry;

    protected $templateLabel = '';

    /**
     * @param FileNode[] $nodes
     * @param string $presetValue
     * @return string
     */
    protected function fillEntries(array $nodes, string $presetValue): string
    {
        $list = HtmlElement::init('ul');
        foreach ($nodes as $subNode) {
            if ($subNode->isDir()) {
                $entry = $this->getSubEntry($subNode);
                $entry->addChild($this->fillEntries($subNode->getSubNodes(), $presetValue));
                $list->addChild($entry);
            } else {
                $list->addChild($this->getEntry($subNode, $presetValue));
            }
        }
        return strval($list);
    }

    protected function getEntry(FileNode $node, string $presetValue): HtmlElement
    {
        $entry = HtmlElement::init('li');
        $input = new Radio();
        $input->set($this->key, $node->getPath(), $node->getName(), ($node->getPath() == $presetValue) ? '1' : 'none');
        $this->inputs[] = $input;
        $entry->addChild($input);
        return $entry;
    }
}
