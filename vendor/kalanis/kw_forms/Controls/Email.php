<?php

namespace kalanis\kw_forms\Controls;


/**
 * Class Email
 * @package kalanis\kw_forms\Controls
 * Form element for email
 */
class Email extends Text
{
    protected string $templateInput = '<input type="email" value="%1$s"%2$s />%3$s';
}
