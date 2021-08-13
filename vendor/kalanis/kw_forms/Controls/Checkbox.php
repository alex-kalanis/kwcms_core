<?php

namespace kalanis\kw_forms\Controls;


/**
 * Class Checkbox
 * @package kalanis\kw_forms\Controls
 * Form element for checkboxes
 */
class Checkbox extends AControl
{
    use TChecked;

    private static $uniqid = 0;
    protected $templateInput = '<input type="checkbox" value="%1$s"%2$s />';

    public function set(string $alias, $value = null, string $label = '')
    {
        $this->setEntry($alias, $value, $label);
        $this->setAttribute('id', sprintf('%s_%s', $this->getKey(), self::$uniqid));
        self::$uniqid++;
        return $this;
    }

    protected function fillTemplate(): string
    {
        return '%2$s %1$s';
    }
}
