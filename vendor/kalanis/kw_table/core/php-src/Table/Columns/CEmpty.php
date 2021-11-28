<?php

namespace kalanis\kw_table\core\Table\Columns;


use kalanis\kw_connect\Interfaces\IRow;


/**
 * Class CEmpty
 * @package kalanis\kw_table\core\Table\Columns
 * Empty column
 */
class CEmpty extends AColumn
{
    public function getValue(IRow $source)
    {
        return '';
    }
}
