<?php

namespace kalanis\kw_table\Table\Rules;


use kalanis\kw_table\Interfaces\Table\IRule;


/**
 * Class Negate
 * @package kalanis\kw_table\Table\Rules
 * This rule negate contained one
 */
class Negate implements IRule
{
    protected $rule = null;

    public function __construct(IRule $rule)
    {
        $this->rule = $rule;
    }

    public function validate($value): bool
    {
        return !$this->rule->validate($value);
    }
}
