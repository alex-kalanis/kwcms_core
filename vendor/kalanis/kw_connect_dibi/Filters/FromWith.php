<?php

namespace kalanis\kw_connect_dibi\Filters;


/**
 * Class FromWith
 * @package kalanis\kw_connect_dibi\Filters
 */
class FromWith extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->dibiFluent->where('%n >= ?', $colName, $value);
        }
        return $this;
    }
}
