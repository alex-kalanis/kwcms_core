<?php

namespace kalanis\kw_connect_doctrine\Filters;


/**
 * Class To
 * @package kalanis\kw_connect_doctrine\Filters
 */
class To extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->queryBuilder->where($colName . ' < ', $this->queryBuilder->createNamedParameter($value));
        }
        return $this;
    }
}
