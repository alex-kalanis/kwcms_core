<?php

namespace kalanis\kw_tree\Controls;


use kalanis\kw_forms\Controls\AControl;


/**
 * Trait TMultiValue
 * @package kalanis\kw_tree\Controls
 */
trait TMultiValue
{
    /** @var AControl[] */
    protected $inputs = [];

    public function getValues(): array
    {
        $array = [];
        foreach ($this->inputs as $child) {
            $array[$child->getKey()] = $child->getValue();
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
            $child->setValue(isset($array[$child->getKey()]) ? $array[$child->getKey()] : '');
        }
    }
}
