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
    /** @var int */
    private static $uniqid = 0;

    protected $templateLabel = '';
    public $templateInput = '<label><input type="radio" value="%1$s"%2$s /> %3$s</label>';

    /**
     * @param string $alias
     * @param string|int|float|bool|null $value
     * @param string $label
     * @param string|int|float|bool|null $checked
     * @return $this
     */
    public function set(string $alias, $value = null, string $label = '', $checked = ''): parent
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
        return $this->wrapIt(sprintf(
            $this->templateInput,
            $this->escaped(strval($this->getOriginalValue())),
            $this->renderAttributes(),
            $this->escaped(strval($this->getLabel()))
        ), $this->wrappersInput);
    }
}
