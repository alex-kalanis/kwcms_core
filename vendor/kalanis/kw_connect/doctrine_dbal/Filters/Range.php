<?php

namespace kalanis\kw_connect\doctrine_dbal\Filters;


use kalanis\kw_connect\core\ConnectException;


/**
 * Class Range
 * @package kalanis\kw_connect\doctrine_dbal\Filters
 */
class Range extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if (!is_array($value) || empty($value) || !isset($value[0]) || !isset($value[1])) {
            throw new ConnectException('Value must be an array of two values with keys 0 and 1.');
        }

        if (!empty($value[0])) {
            $this->getSource()->where($this->getSource()->expr()->gt(
                $colName,
                $this->getSource()->createNamedParameter($value[0])
            ));
        }
        if (!empty($value[1])) {
            $this->getSource()->where($this->getSource()->expr()->lt(
                $colName,
                $this->getSource()->createNamedParameter($value[1])
            ));
        }

        return $this;
    }
}
