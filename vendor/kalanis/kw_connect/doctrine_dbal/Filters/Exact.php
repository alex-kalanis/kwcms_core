<?php

namespace kalanis\kw_connect\doctrine_dbal\Filters;


/**
 * Class Exact
 * @package kalanis\kw_connect\doctrine_dbal\Filters
 */
class Exact extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->where($this->getSource()->expr()->eq(
                $colName,
                $this->getSource()->createNamedParameter($value)
            ));
        }
        return $this;
    }
}
