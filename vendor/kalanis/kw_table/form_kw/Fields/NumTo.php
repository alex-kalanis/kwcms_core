<?php

namespace kalanis\kw_table\form_kw\Fields;


use kalanis\kw_connect\core\Interfaces\IFilterFactory;


/**
 * Class NumTo
 * @package kalanis\kw_table\form_kw\Fields
 */
class NumTo extends AField
{
    protected function getFilterAction(): string
    {
        return IFilterFactory::ACTION_TO;
    }

    public function add(): void
    {
        $this->form->addText($this->alias, '', null, $this->attributes);
    }
}
