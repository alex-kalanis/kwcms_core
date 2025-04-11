<?php

namespace KWCMS\modules\Core\Libs;


use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_rules\Exceptions\RuleException;


trait TFormErrors
{
    /**
     * @param array<string, array<int, RuleException>> $in
     * @return FormsException
     */
    protected function parseErrors(array $in): FormsException
    {
        $flatten = [];
        foreach ($in as $items) {
            foreach ($items as $item) {
                $flatten[] = $item->getMessage();
            }
        }
        return new FormsException(implode(',', $flatten));
    }
}
