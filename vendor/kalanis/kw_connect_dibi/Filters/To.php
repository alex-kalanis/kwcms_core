<?php

namespace kalanis\kw_connect_dibi\Filters;


/**
 * Class To
 * @package kalanis\kw_connect_dibi\Filters
 */
class To extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->dibiFluent->where('%n < ?', $colName, $value);
        }
        return $this;
    }
}
