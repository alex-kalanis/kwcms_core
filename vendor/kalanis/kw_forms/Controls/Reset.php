<?php

namespace kalanis\kw_forms\Controls;


/**
 * Class Reset
 * @package kalanis\kw_forms\Controls
 * Form element for reset button
 */
class Reset extends Button
{
    protected string $templateInput = '<input type="reset" value="%1$s"%2$s />';
    protected $originalValue = 'reset';
}
