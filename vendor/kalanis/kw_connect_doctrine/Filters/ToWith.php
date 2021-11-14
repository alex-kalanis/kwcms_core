<?php

namespace kalanis\kw_connect_doctrine\Filters;


/**
 * Class ToWith
 * @package kalanis\kw_connect_doctrine\Filters
 */
class ToWith extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->queryBuilder->where($colName . ' <= ', $this->queryBuilder->createNamedParameter($value));
        }
        return $this;
    }
}
