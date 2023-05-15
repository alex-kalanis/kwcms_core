<?php

namespace kalanis\kw_tree_controls\Controls;


use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\Essentials\FileNode;
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
        if (!is_null($this->getLabel())) {
            $legend->addChild($this->getLabel());
        }
        $div->addChild($this->fillEntries([$baseNode]));
        $fieldset->addChild($legend);
        $fieldset->addChild($div);
        return $fieldset->render();
    }

    protected function getSubEntry(ControlNode $node): HtmlElement
    {
        $entry = HtmlElement::init('li', ['value' => $this->stringPath($node->getNode())]);
        $label = HtmlElement::init('label', ['class' => 'dir']);
        $label->addChild($this->stringName($node->getNode()));
        $entry->addChild($label);
        return $entry;
    }

    abstract public function getLabel(): ?string;

    abstract public function addChild($child, $alias = null, bool $merge = false, bool $inherit = false): void;

    abstract protected function fillEntries(array $nodes): string;

    abstract protected function stringName(?FileNode $node): string;

    abstract protected function stringPath(?FileNode $node): string;
}
