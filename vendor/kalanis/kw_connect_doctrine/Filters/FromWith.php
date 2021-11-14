<?php

namespace kalanis\kw_connect_doctrine\Filters;


/**
 * Class FromWith
 * @package kalanis\kw_connect_doctrine\Filters
 */
class FromWith extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->queryBuilder->where($colName . ' >= ', $this->queryBuilder->createNamedParameter($value));
        }
        return $this;
    }
}
