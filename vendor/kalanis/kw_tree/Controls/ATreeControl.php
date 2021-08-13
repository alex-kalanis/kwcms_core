<?php

namespace kalanis\kw_tree\Controls;


use kalanis\kw_forms\Controls\AControl;
use kalanis\kw_tree\FileNode;


/**
 * Class ATreeControl
 * @package kalanis\kw_tree\Controls
 */
abstract class ATreeControl extends AControl
{
    protected $templateInput = '%1$s'; // by our own!
    protected $tree = null;
    /** @var AControl[] */
    protected $inputs = [];

    public function set(string $key, ?string $value = null, string $label = '', ?FileNode $tree = null)
    {
        $this->setEntry($key, $value, $label);
        $this->tree = $tree;
        return $this;
    }

    public function renderInput($attributes = null): string
    {
        $this->addAttributes($attributes);
        if (!empty($this->value) && ($this->value != $this->originalValue)) {
            $value = $this->value;
        } else {
            $value = $this->originalValue;
        }
        return $this->wrapIt(sprintf($this->templateInput, $this->renderTree($this->tree, $value)), $this->wrappersInput);
    }

    abstract protected function renderTree(?FileNode $baseNode, string $presetValue): string;
}
