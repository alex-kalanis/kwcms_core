<?php

namespace kalanis\kw_connect\doctrine_dbal\Filters;


/**
 * Class FromWith
 * @package kalanis\kw_connect\doctrine_dbal\Filters
 */
class FromWith extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->where($this->getSource()->expr()->gte(
                $colName,
                $this->getSource()->createNamedParameter($value)
            ));
        }
        return $this;
    }
}
