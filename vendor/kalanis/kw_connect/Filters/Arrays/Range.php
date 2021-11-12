<?php

namespace kalanis\kw_connect\Filters\Arrays;


use kalanis\kw_connect\ConnectException;
use kalanis\kw_connect\Interfaces\IRow;


/**
 * Class Range
 * @package kalanis\kw_connect\Filters\Arrays
 */
class Range extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if (!is_array($value) || empty($value) || !isset($value[0]) || !isset($value[1])) {
            throw new ConnectException('Value must be an array of two values with keys 0 and 1.');
        }

        $this->dataSource->setArray(array_filter($this->dataSource->getArray(), function (IRow $item) use ($colName, $value) {
            $itemValue = $item->getValue($colName);
            return $itemValue > $value[0] && $itemValue < $value[1];
        }));
        return $this;
    }
}
