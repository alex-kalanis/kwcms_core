<?php

namespace kalanis\kw_table\form_kw\Fields;


use kalanis\kw_connect\core\Interfaces\IFilterFactory;


/**
 * Class NumToWith
 * @package kalanis\kw_table\form_kw\Fields
 */
class NumToWith extends AField
{
    protected function getFilterAction(): string
    {
        return IFilterFactory::ACTION_TO_WITH;
    }

    public function add(): void
    {
        $this->form->addText($this->alias, '', null, $this->attributes);
    }
}