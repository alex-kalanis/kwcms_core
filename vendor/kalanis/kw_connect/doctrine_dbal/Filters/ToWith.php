<?php

namespace kalanis\kw_connect\doctrine_dbal\Filters;


/**
 * Class ToWith
 * @package kalanis\kw_connect\doctrine_dbal\Filters
 */
class ToWith extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->where($this->getSource()->expr()->lte(
                $colName,
                $this->getSource()->createNamedParameter($value)
            ));
        }
        return $this;
    }
}
