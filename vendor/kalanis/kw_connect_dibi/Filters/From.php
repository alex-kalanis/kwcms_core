<?php

namespace kalanis\kw_connect_dibi\Filters;


/**
 * Class From
 * @package kalanis\kw_connect_dibi\Filters
 */
class From extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->dibiFluent->where('%n > ?', $colName, $value);
        }
        return $this;
    }
}
