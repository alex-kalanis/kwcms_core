<?php

namespace kalanis\kw_connect\doctrine_dbal\Filters;


/**
 * Class To
 * @package kalanis\kw_connect\doctrine_dbal\Filters
 */
class To extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->where($this->getSource()->expr()->lt(
                $colName,
                $this->getSource()->createNamedParameter($value)
            ));
        }
        return $this;
    }
}
