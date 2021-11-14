<?php

namespace kalanis\kw_connect_nette\Filters;


/**
 * Class To
 * @package kalanis\kw_connect_nette\Filters
 */
class To extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->netteTable->where($colName . ' < ?', $value);
        }
        return $this;
    }
}
