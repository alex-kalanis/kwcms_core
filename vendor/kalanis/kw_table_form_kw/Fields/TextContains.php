<?php

namespace kalanis\kw_table_form_kw\Fields;


use kalanis\kw_connect\Interfaces\IFilterFactory;


/**
 * Class TextContains
 * @package kalanis\kw_table_form_kw\Fields
 */
class TextContains extends AField
{
    protected function getFilterAction(): string
    {
        return IFilterFactory::ACTION_CONTAINS;
    }

    public function add(): void
    {
        $this->form->addText($this->alias, '', null, $this->attributes);
    }
}
