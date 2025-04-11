<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_forms\Interfaces\IMultiValue;
use kalanis\kw_rules\Exceptions\RuleException;
use kalanis\kw_rules\Interfaces\IValidate;
use kalanis\kw_rules\Rules\ARule;


class MultiRule extends ARule
{
    public function validate(IValidate $entry): void
    {
        if (!is_a($entry, IMultiValue::class)) {
            throw new RuleException('Not a multivalue!');
        }
        $isEmpty = true;
        foreach ($entry->getValues() as $item) {
            if (!empty($item)) {
                $isEmpty = false;
            }
        }
        if ($isEmpty) {
            throw new RuleException($this->errorText);
        }
    }
}
