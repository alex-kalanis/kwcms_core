<?php

namespace kalanis\kw_tree\Controls;


use kalanis\kw_forms\Controls;


/**
 * Trait TMultiValue
 * @package kalanis\kw_tree\Controls
 */
trait TMultiValue
{
    use Controls\TShorterKey;

    /** @var Controls\AControl[]|Controls\Checkbox[] */
    protected $inputs = [];

    public function getValues(): array
    {
        $array = [];
        foreach ($this->inputs as $child) {
            if (empty($child->getValue())) {
                continue;
            }
            $array[] = $child->getValue();
        }
        return $array;
    }

    /**
     * Set values to all children
     * !! UNDEFINED values will be SET too !!
     * @param string[]|array $array
     */
    public function setValues(array $array): void
    {
        foreach ($this->inputs as $child) {
            $shortKey = $this->shorterKey($child->getKey());
            $child->setValue(
                isset($array[$shortKey])
                && is_array($array[$shortKey])
                && in_array($child->getOriginalValue(), $array[$shortKey])
                    ? $child->getOriginalValue()
                    : (
                    isset($array[$child->getKey()])
                    && is_array($array[$child->getKey()])
                    && in_array($child->getOriginalValue(), $array[$child->getKey()])
                        ? $child->getOriginalValue()
                        : ''
                )
            );
        }
    }
}
