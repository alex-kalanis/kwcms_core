<?php

namespace kalanis\kw_connect\arrays\Filters;


use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class To
 * @package kalanis\kw_connect\core\Filters\Arrays
 */
class To extends AType
{
    public function setFiltering(string $colName, $value)
    {
        $this->getSource()->setArray(array_filter($this->getSource()->getArray(), function (IRow $item) use ($colName, $value) {
            return $item->getValue($colName) < $value;
        }));
        return $this;
    }
}
