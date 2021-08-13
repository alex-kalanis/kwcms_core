<?php

namespace kalanis\kw_rules\Rules;


use kalanis\kw_rules\Exceptions\RuleException;


/**
 * trait TCheckArrayRange
 * @package kalanis\kw_rules\Rules
 * Check original values as array of ranges
 */
trait TCheckArrayRange
{
    use TRule;

    protected function checkValue($againstValue)
    {
        if (!is_array($againstValue)) {
            throw new RuleException('No array found. Need set input as array!');
        }
        return array_map([$this, 'checkRule'], $againstValue);
    }

    /**
     * @param mixed $againstValue
     * @return array
     * @throws RuleException
     */
    protected function checkRule($againstValue): array
    {
        if (!is_array($againstValue)) {
            throw new RuleException('No array found. Need set both values to compare!');
        }
        return array_map('intval', $againstValue);
    }
}
