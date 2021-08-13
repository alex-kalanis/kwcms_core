<?php

namespace kalanis\kw_rules\Rules;


use kalanis\kw_rules\Interfaces\IValidate;
use kalanis\kw_rules\Exceptions\RuleException;


/**
 * Class ARule
 * @package kalanis\kw_rules\Rules
 * Basic abstraction for checking rules
 */
abstract class ARule
{
    use TRule;

    /**
     * @param IValidate $entry
     * @return void
     * @throws RuleException
     */
    abstract public function validate(IValidate $entry): void;
}
