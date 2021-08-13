<?php

namespace kalanis\kw_rules\Rules;


use kalanis\kw_rules\Exceptions\RuleException;


/**
 * Trait TRule
 * @package kalanis\kw_rules\Rules
 * Abstract for checking input - What is available for both usual inputs and files
 */
trait TRule
{
    protected $againstValue = null;

    protected $errorText = '';

    /**
     * @param mixed $againstValue
     * @return self
     * @throws RuleException
     */
    public function setAgainstValue($againstValue): void
    {
        $this->againstValue = $this->checkValue($againstValue);
    }

    /**
     * @param mixed $againstValue
     * @return mixed
     * @throws RuleException
     * Nothing here, but more in children, especially in their traits
     */
    protected function checkValue($againstValue)
    {
        return $againstValue;
    }

    public function setErrorText(string $errorText): void
    {
        $this->errorText = $errorText;
    }
}
