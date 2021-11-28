<?php

namespace kalanis\kw_connect\core\Rows;


use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class Arrays
 * @package kalanis\kw_connect\core\Rows
 */
class Arrays implements IRow
{
    protected $array;

    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function getValue($property)
    {
        return $this->array[$property];
    }

    public function __isset($name)
    {
        return isset($this->array[$name]);
    }
}
