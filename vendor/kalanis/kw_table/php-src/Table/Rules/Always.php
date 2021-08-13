<?php

namespace kalanis\kw_table\Table\Rules;


use kalanis\kw_table\Interfaces\Table\IRule;


/**
 * Class Always
 * @package kalanis\kw_table\Table\Rules
 * This rule came always true
 */
class Always extends ARule implements IRule
{
    public function validate($value): bool
    {
        return true;
    }
}
