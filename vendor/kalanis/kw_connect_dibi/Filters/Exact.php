<?php

namespace kalanis\kw_connect_dibi\Filters;


/**
 * Class Exact
 * @package kalanis\kw_connect_dibi\Filters
 */
class Exact extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->dibiFluent->where('%n = ?', $colName, $value);
        }
        return $this;
    }
}
