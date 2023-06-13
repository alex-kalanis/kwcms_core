<?php

namespace kalanis\kw_tree_controls\Controls;


use kalanis\kw_forms\Controls\AControl;
use kalanis\kw_forms\Controls\SelectOption;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_templates\HtmlElement;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree_controls\ControlNode;


/**
 * Class FileSelect
 * @package kalanis\kw_tree_controls\Controls
 */
class FileSelect extends ATreeControl
{
    use TSimpleValue;

    /** @var bool */
    protected $wantEmptySub = false;

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
        $select->addChild($this->getOptionGroup($baseNode));
        return $select->render();
    }

    /**
     * @param ControlNode $node
     * @throws RenderException
     * @return string
     */
    protected function getOptionGroup(ControlNode $node): string
    {
        if ($node->getNode() && $node->getNode()->isDir()) {
            $group = HtmlElement::init('optgroup', ['label' => $this->stringPath($node->getNode())]);
            foreach ($node->getSubNodes() as $subNode) {
                $group->addChild($this->getOptionGroup($subNode));
            }
            return strval($group);
        } else {
            return $this->getOption($node);
        }
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

    protected function getInput(FileNode $node): AControl
    {
        $input = new SelectOption();
        $input->setEntry($this->getKey(), $this->stringPath($node), $this->stringName($node));
        $this->inputs[] = $input;
        return $input;
    }
}
