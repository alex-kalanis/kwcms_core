<?php

namespace kalanis\kw_connect_doctrine\Filters;


/**
 * Class Exact
 * @package kalanis\kw_connect_doctrine\Filters
 */
class Exact extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->queryBuilder->where($colName . ' = ', $this->queryBuilder->createNamedParameter($value));
        }
        return $this;
    }
}
