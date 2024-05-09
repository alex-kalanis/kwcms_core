<?php

namespace kalanis\kw_connect\eloquent\Filters;


/**
 * Class ToWith
 * @package kalanis\kw_connect\eloquent\Filters
 */
class ToWith extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->where($colName, '<=', $value);
        }
        return $this;
    }
}
