<?php

namespace kalanis\kw_table\form_kw\Fields;


use kalanis\kw_connect\core\Interfaces\IFilterFactory;


/**
 * Class NumFromWith
 * @package kalanis\kw_table\form_kw\Fields
 */
class NumFromWith extends AField
{
    protected function getFilterAction(): string
    {
        return IFilterFactory::ACTION_FROM_WITH;
    }

    public function add(): void
    {
        $this->form->addText($this->alias, '', null, $this->attributes);
    }
}
