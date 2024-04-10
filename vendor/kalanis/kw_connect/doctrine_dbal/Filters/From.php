<?php

namespace kalanis\kw_connect\doctrine_dbal\Filters;


/**
 * Class From
 * @package kalanis\kw_connect\doctrine_dbal\Filters
 */
class From extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->where($this->getSource()->expr()->gt(
                $colName,
                $this->getSource()->createNamedParameter($value)
            ));
        }
        return $this;
    }
}
