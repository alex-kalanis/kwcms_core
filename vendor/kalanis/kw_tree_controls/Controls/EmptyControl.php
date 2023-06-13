<?php

namespace kalanis\kw_tree_controls\Controls;


use kalanis\kw_forms\Controls\Text;
use kalanis\kw_forms\Exceptions\RenderException;


/**
 * Class EmptyControl
 * @package kalanis\kw_tree_controls\Controls
 * Just when you need control entity without that control input
 */
class EmptyControl extends Text
{
    protected $templateInput = '%3$s';

    public function getValue()
    {
        return null;
    }

    public function setValue(/** @scrutinizer ignore-unused */ $value): void
    {
        // intentionally nothing
    }

    /**
     * Return input entry in HTML
     * @param string|string[]|array|null $attributes
     * @throws RenderException
     * @return string
     */
    public function renderInput($attributes = null): string
    {
        return $this->wrapIt(sprintf($this->templateInput, '', '', $this->renderChildren()), $this->wrappersInput);
    }
}
