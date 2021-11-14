<?php

namespace kalanis\kw_connect_dibi;


use Dibi;
use kalanis\kw_connect\Interfaces\IRow;


/**
 * Class Row
 * @package kalanis\kw_connect_dibi
 */
class Row implements IRow
{
    protected $row;

    public function __construct(Dibi\Row $row)
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
