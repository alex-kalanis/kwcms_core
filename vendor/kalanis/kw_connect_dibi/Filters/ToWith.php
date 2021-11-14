<?php

namespace kalanis\kw_connect_dibi\Filters;


/**
 * Class ToWith
 * @package kalanis\kw_connect_dibi\Filters
 */
class ToWith extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->dibiFluent->where('%n <= ?', $colName, $value);
        }
        return $this;
    }
}
