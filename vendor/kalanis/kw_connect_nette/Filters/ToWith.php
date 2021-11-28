<?php

namespace kalanis\kw_connect_nette\Filters;


/**
 * Class ToWith
 * @package kalanis\kw_connect_nette\Filters
 */
class ToWith extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->netteTable->where($colName . ' <= ?', $value);
        }
        return $this;
    }
}