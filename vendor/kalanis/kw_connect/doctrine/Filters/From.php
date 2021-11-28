<?php

namespace kalanis\kw_connect\doctrine\Filters;


/**
 * Class From
 * @package kalanis\kw_connect\doctrine\Filters
 */
class From extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->queryBuilder->where($colName . ' > ', $this->queryBuilder->createNamedParameter($value));
        }
        return $this;
    }
}
