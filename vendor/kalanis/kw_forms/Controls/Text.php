<?php

namespace kalanis\kw_forms\Controls;


/**
 * Class Text
 * @package kalanis\kw_forms\Controls
 * Form element for text
 */
class Text extends AControl
{
    protected $templateInput = '<input type="text" value="%1$s"%2$s />%3$s';

    public function set(string $alias, ?string $value = null, string $label = ''): self
    {
        $this->setEntry($alias, $value, $label);
        $this->setAttribute('id', $this->getKey());
        return $this;
    }
}
