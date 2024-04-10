<?php

namespace kalanis\kw_connect\core\Rows;


use ArrayAccess;
use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class ArrayAccessRow
 * @package kalanis\kw_connect\core\Rows
 */
class ArrayAccessRow implements IRow
{
    protected ArrayAccess $row;

    public function __construct(ArrayAccess $row)
    {
        $this->row = $row;
    }

    public function getValue($property)
    {
        return $this->row->offsetGet($property);
    }

    public function __isset($name)
    {
        return $this->row->offsetExists($name);
    }
}
