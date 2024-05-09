<?php

namespace kalanis\kw_rules;


/**
 * Class SubRules
 * @package kalanis\kw_rules
 * For generating trees of rules
 * This class offers simplified making of branches
 */
class SubRules
{
    use TRules;

    protected function whichRulesFactory(): Interfaces\IRuleFactory
    {
        return new Rules\Factory();
    }
}
