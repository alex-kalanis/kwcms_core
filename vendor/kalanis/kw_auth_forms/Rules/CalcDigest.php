<?php

namespace kalanis\kw_auth_forms\Rules;


use kalanis\kw_auth_forms\Interfaces\IMethod;
use kalanis\kw_rules\Exceptions\RuleException;
use kalanis\kw_rules\Interfaces\IValidate;
use kalanis\kw_rules\Rules\ARule;
use kalanis\kw_rules\Rules\TRule;


/**
 * Class CalcDigest
 * @package kalanis\kw_auth_forms\Rules
 * Calculate digest rule for inputs
 */
class CalcDigest extends ARule
{
    use TRule;

    /** @var IMethod|null */
    protected $authMethod = null;

    public function __construct(IMethod $authMethod)
    {
        $this->authMethod = $authMethod;
    }

    public function validate(IValidate $entry): void
    {
        if (!$this->authMethod->check(strval($entry->getValue()), strval($this->againstValue))) {
            throw new RuleException($this->errorText);
        }
    }
}
