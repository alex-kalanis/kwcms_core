<?php

namespace kalanis\kw_forms\Controls;


/**
 * Class Button
 * @package kalanis\kw_forms\Controls
 * Form element for button
 */
class Button extends AControl
{
    protected string $templateLabel = '';
    protected string $templateInput = '<input type="button" value="%1$s"%2$s />';
    protected $originalValue = 'button';

    public function set(string $alias, string $title = ''): self
    {
        if (empty($title) && !empty($alias)) {
            $title = $alias;
        }
        $title = empty($title) ? strval($this->originalValue) : $title ;
        if (empty($alias)) {
            $alias = $title;
        }
        $this->setEntry($alias, $title, $title);
        $this->setAttribute('id', $this->getKey());
        return $this;
    }
}
