<?php

namespace kalanis\kw_tree_controls\Controls;


use kalanis\kw_forms\Controls;


/**
 * Trait TSimpleValue
 * @package kalanis\kw_tree_controls\Controls
 */
trait TSimpleValue
{
    /** @var Controls\AControl[]|Controls\Checkbox[]|Controls\SelectOption[] */
    protected array $inputs = [];

    public function getValue()
    {
        foreach ($this->inputs as $child) {
            $value = $child->getValue();
            if ($value) {
                return $value;
            }
        }
        return '';
    }

    /**
     * Set value to all children
     * !! UNDEFINED values will be SET too !!
     * @param string $value
     */
    public function setValue($value): void
    {
        foreach ($this->inputs as $child) {
            $child->setValue($value);
        }
    }
}
