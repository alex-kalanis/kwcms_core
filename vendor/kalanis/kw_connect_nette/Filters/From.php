<?php

namespace kalanis\kw_connect_nette\Filters;


/**
 * Class From
 * @package kalanis\kw_connect_nette\Filters
 */
class From extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->netteTable->where($colName . ' > ?', $value);
        }
        return $this;
    }
}
