<?php

namespace kalanis\kw_tree\Controls;


use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\FileNode;


/**
 * Trait TSubEntry
 * @package kalanis\kw_tree\Controls
 */
trait TSubEntry
{
    protected function renderTree(?FileNode $baseNode): string
    {
        if (empty($baseNode)) {
            return '';
        }
        $fieldset = HtmlElement::init('fieldset');
        $legend = HtmlElement::init('legend');
        $div = HtmlElement::init('div', ['class' => 'select_dir']);
        $legend->addChild($this->getLabel());
        $div->addChild($this->fillEntries([$baseNode]));
        $fieldset->addChild($legend);
        $fieldset->addChild($div);
        return $fieldset->render();
    }

    protected function getSubEntry(FileNode $node): HtmlElement
    {
        $entry = HtmlElement::init('li', ['value' => $node->getPath()]);
        $label = HtmlElement::init('label', ['class' => 'dir']);
        $label->addChild($node->getName());
        $entry->addChild($label);
        return $entry;
    }

    abstract protected function fillEntries(array $nodes): string;
}
