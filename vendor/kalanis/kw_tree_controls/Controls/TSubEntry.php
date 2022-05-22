<?php

namespace kalanis\kw_tree_controls\Controls;


use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree_controls\ControlNode;


/**
 * Trait TSubEntry
 * @package kalanis\kw_tree_controls\Controls
 */
trait TSubEntry
{
    protected function renderTree(?ControlNode $baseNode): string
    {
        if (empty($baseNode)) {
            return '';
        }
        $fieldset = HtmlElement::init('fieldset');
        $legend = HtmlElement::init('legend');
        $div = HtmlElement::init('div', ['class' => 'select_tree']);
        $legend->addChild($this->getLabel());
        $div->addChild($this->fillEntries([$baseNode]));
        $fieldset->addChild($legend);
        $fieldset->addChild($div);
        return $fieldset->render();
    }

    protected function getSubEntry(ControlNode $node): HtmlElement
    {
        $entry = HtmlElement::init('li', ['value' => $node->getNode()->getPath()]);
        $label = HtmlElement::init('label', ['class' => 'dir']);
        $label->addChild($node->getNode()->getName());
        $entry->addChild($label);
        return $entry;
    }

    abstract protected function fillEntries(array $nodes): string;
}
