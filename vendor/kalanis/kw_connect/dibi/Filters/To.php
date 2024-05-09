<?php

namespace kalanis\kw_connect\dibi\Filters;


/**
 * Class To
 * @package kalanis\kw_connect\dibi\Filters
 */
class To extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->where($colName . ' < ?', $value);
        }
        return $this;
    }
}
