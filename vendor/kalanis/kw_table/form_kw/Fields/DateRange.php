<?php

namespace kalanis\kw_table\form_kw\Fields;


use kalanis\kw_connect\core\Interfaces\IFilterFactory;


/**
 * Class DateRange
 * @package kalanis\kw_table\form_kw\Fields
 */
class DateRange extends AField
{
    protected function getFilterAction(): string
    {
        return IFilterFactory::ACTION_RANGE;
    }

    public function add(): void
    {
        $this->form->addDateRange($this->alias, '', null, $this->attributes);
    }
}
