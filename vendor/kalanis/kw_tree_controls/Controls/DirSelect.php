<?php

namespace kalanis\kw_tree_controls\Controls;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree_controls\ControlNode;


/**
 * Class DirSelect
 * @package kalanis\kw_tree_controls\Controls
 */
class DirSelect extends ATreeControl
{
    use TSimpleValue;

    protected bool $wantEmptySub = false;

    /**
     * @param ControlNode|null $baseNode
     * @throws RenderException
     * @return string
     */
    protected function renderTree(?ControlNode $baseNode): string
    {
        if (empty($baseNode)) {
            return '';
        }
        $select = HtmlElement::init('select');
        $select->setAttribute('name', $this->key);
        $select->addChild($this->fillOptions([$baseNode]));
        return $select->render();
    }

    /**
     * @param ControlNode[] $nodes
     * @throws RenderException
     * @return string
     */
    protected function fillOptions(array $nodes): string
    {
        $result = [];
        foreach ($nodes as $subNode) {
            $result[] = $this->getOption($subNode)
                . $this->fillOptions($subNode->getSubNodes());
        }
        return implode('', $result);
    }

    /**
     * @param ControlNode $node
     * @throws RenderException
     * @return string
     */
    protected function getOption(ControlNode $node): string
    {
        return $node->getControl() ? $node->getControl()->render() : '';
    }

    protected function getInput(FileNode $node): Controls\AControl
    {
        $path = $this->stringPath($node);
        $input = new Controls\SelectOption();
        $input->setEntry($this->getKey(), $path, $path . DIRECTORY_SEPARATOR);
        $this->inputs[] = $input;
        return $input;
    }
}
