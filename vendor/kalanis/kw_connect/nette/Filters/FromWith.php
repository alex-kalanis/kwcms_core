<?php

namespace kalanis\kw_connect\nette\Filters;


/**
 * Class FromWith
 * @package kalanis\kw_connect\nette\Filters
 */
class FromWith extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->where($colName . ' >= ?', $value);
        }
        return $this;
    }
}
