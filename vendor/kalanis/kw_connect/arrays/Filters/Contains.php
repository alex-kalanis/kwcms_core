<?php

namespace kalanis\kw_connect\arrays\Filters;


use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class Contains
 * @package kalanis\kw_connect\core\Filters\Arrays
 */
class Contains extends AType
{
    public function setFiltering(string $colName, $value)
    {
        $this->getSource()->setArray(array_filter($this->getSource()->getArray(), function (IRow $item) use ($colName, $value) {
            return preg_match('#' . preg_quote(strval($value), '#') . '#', $item->getValue($colName));
        }));
        return $this;
    }
}
