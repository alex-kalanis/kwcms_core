<?php

namespace kalanis\kw_connect\eloquent\Filters;


/**
 * Class From
 * @package kalanis\kw_connect\eloquent\Filters
 */
class From extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->where($colName, '>', $value);
        }
        return $this;
    }
}
