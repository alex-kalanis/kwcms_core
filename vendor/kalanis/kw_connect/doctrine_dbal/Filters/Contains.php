<?php

namespace kalanis\kw_connect\doctrine_dbal\Filters;


/**
 * Class Contains
 * @package kalanis\kw_connect\doctrine_dbal\Filters
 */
class Contains extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->where($this->getSource()->expr()->like(
                $colName,
                $this->getSource()->createNamedParameter($value)
            ));
        }
        return $this;
    }
}
