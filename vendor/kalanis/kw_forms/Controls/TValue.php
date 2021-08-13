<?php

namespace kalanis\kw_forms\Controls;


trait TValue
{
    /** @var string */
    protected $value = '';

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
