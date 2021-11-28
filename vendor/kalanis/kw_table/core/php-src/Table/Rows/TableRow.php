<?php

namespace kalanis\kw_table\core\Table\Rows;


/**
 * Class TableRow
 * @package kalanis\kw_table\core\Table\Rows
 * Input is another table
 */
class TableRow extends ARow
{
    public function __construct(callable $funcName, array $funcArgs)
    {
        $this->setFunctionName($funcName);
        $this->setFunctionArgs($funcArgs);
    }
}