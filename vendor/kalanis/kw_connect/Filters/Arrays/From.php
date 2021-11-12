<?php

namespace kalanis\kw_connect\Filters\Arrays;


use kalanis\kw_connect\Interfaces\IRow;


/**
 * Class From
 * @package kalanis\kw_connect\Filters\Arrays
 */
class From extends AType
{
    /**
     * @param string           $colName
     * @param string|int|float $value
     * @return $this
     */
    public function setFiltering($colName, $value)
    {
        $this->dataSource->setArray(array_filter($this->dataSource->getArray(), function (IRow $item) use ($colName, $value) {
            return $item->getValue($colName) > $value;
        }));
        return $this;
    }
}
