<?php

namespace kalanis\kw_connect_doctrine\Filters;


/**
 * Class Contains
 * @package kalanis\kw_connect_doctrine\Filters
 */
class Contains extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->queryBuilder->where($colName . ' LIKE ', $this->queryBuilder->createNamedParameter($value));
        }
        return $this;
    }
}
