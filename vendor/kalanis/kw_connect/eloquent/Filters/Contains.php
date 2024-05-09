<?php

namespace kalanis\kw_connect\eloquent\Filters;


/**
 * Class Contains
 * @package kalanis\kw_connect\eloquent\Filters
 */
class Contains extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->where($colName, 'LIKE', '%' . $value);
        }
        return $this;
    }
}
