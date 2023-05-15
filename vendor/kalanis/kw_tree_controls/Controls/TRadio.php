<?php

namespace kalanis\kw_tree_controls\Controls;


/**
 * Trait TRadio
 * @package kalanis\kw_tree_controls\Controls
 * Trait for accessing grouped radio buttons as single element
 */
trait TRadio
{
    /** @var Radio[] */
    protected $inputs = [];

    public function getValue()
    {
        foreach ($this->inputs as $input) {
            /** @var Radio $input */
            if ($input->getAttribute('checked')) {
                return $input->getOriginalValue();
            }
        }
        return null;
    }

    public function setValue($value): void
    {
        foreach ($this->inputs as $input) {
            /** @var Radio $input */
            if ($input->getOriginalValue() == $value) {
                $input->setAttribute('checked', 'checked');
            } else {
                $input->removeAttribute('checked');
            }
        }
    }
}
