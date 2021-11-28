<?php

namespace kalanis\kw_connect_nette\Filters;


/**
 * Class Contains
 * @package kalanis\kw_connect_nette\Filters
 */
class Contains extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->netteTable->where($colName . ' LIKE ?', $value);
        }
        return $this;
    }
}