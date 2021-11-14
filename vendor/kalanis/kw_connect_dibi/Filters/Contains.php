<?php

namespace kalanis\kw_connect_dibi\Filters;


/**
 * Class Contains
 * @package kalanis\kw_connect_dibi\Filters
 */
class Contains extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->dibiFluent->where('%n LIKE %~like~', $colName, $value);
        }
        return $this;
    }
}
