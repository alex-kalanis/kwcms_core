<?php

namespace kalanis\kw_tree_controls\Controls;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Controls\RadioSet;


/**
 * Class Radio
 * @package kalanis\kw_tree_controls\Controls
 * Radio with simplified label
 */
class Radio extends Controls\Radio
{
    private static $uniqid = 0;
    protected $templateLabel = '';
    public $templateInput = '<label><input type="radio" value="%1$s"%2$s /> %3$s</label>';

    public function set(string $alias, $value = null, string $label = '', $checked = '')
    {
        $this->setEntry($alias, $value, $label);
        $this->setChecked($checked);
        $this->setAttribute('id', sprintf('%s_%s', $this->getKey(), self::$uniqid));
        self::$uniqid++;
        return $this;
    }

    public function renderInput($attributes = null): string
    {
        $this->fillParent();
        $this->addAttributes($attributes);
        if (!($this->parent instanceof RadioSet)) {
            $this->setAttribute('name', $this->getKey());
        }
        return $this->wrapIt(sprintf($this->templateInput, $this->escaped(strval($this->getOriginalValue())), $this->renderAttributes(), $this->escaped(strval($this->getLabel()))), $this->wrappersInput);
    }
}
