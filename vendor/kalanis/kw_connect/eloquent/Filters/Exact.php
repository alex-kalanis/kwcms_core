<?php

namespace kalanis\kw_connect\eloquent\Filters;


/**
 * Class Exact
 * @package kalanis\kw_connect\eloquent\Filters
 */
class Exact extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->where($colName, '=', $value);
        }
        return $this;
    }
}
