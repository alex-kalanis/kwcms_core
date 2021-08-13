<?php

namespace kalanis\kw_rules\Rules;


use kalanis\kw_rules\Exceptions\RuleException;


/**
 * trait TCheckRules
 * @package kalanis\kw_rules\Rules
 * Check original values as set of rules
 */
trait TCheckRules
{
    use TRule;

    protected function checkValue($againstValue)
    {
        if (!is_array($againstValue)) {
            throw new RuleException('No array found. Need set matching rules!');
        }
        return array_map([$this, 'checkRule'], $againstValue);
    }

    /**
     * @param mixed $singleRule
     * @return ARule|File\AFileRule
     * @throws RuleException
     */
    protected function checkRule($singleRule)
    {
        if (!is_object($singleRule)) {
            throw new RuleException('Input is not an object.');
        }
        if (! ( ($singleRule instanceof ARule) || ($singleRule instanceof File\AFileRule) ) ) {
            throw new RuleException(sprintf('Input %s is not instance of ARule or AFileRule.', get_class($singleRule)));
        }
        return $singleRule;
    }
}
