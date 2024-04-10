<?php

namespace kalanis\kw_connect\eloquent;


use ArrayIterator;
use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class Row
 * @package kalanis\kw_connect\eloquent
 */
class Row implements IRow
{
    protected ArrayIterator $row;

    public function __construct(ArrayIterator $row)
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
