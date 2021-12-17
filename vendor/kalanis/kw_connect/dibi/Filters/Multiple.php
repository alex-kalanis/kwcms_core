<?php

namespace kalanis\kw_connect\dibi\Filters;


use kalanis\kw_connect\core\Interfaces\IFilterSubs;
use kalanis\kw_connect\core\Interfaces\IFilterType;


/**
 * Class Multiple
 * @package kalanis\kw_connect\dibi\Filters
 */
class Multiple extends AType implements IFilterSubs
{
    /** @var IFilterType[] */
    protected $subFilters = [];

    public function addSubFilter(string $alias, IFilterType $filter): void
    {
        $this->subFilters[$alias] = $filter;
    }

    public function setFiltering(string $colName, $value)
    {
        foreach ($this->subFilters as $alias => &$subFilter) {
            if (isset($value[$alias]) && (IFilterType::EMPTY_FILTER != $value[$alias])) {
                $subFilter->setDataSource($this->dibiFluent);
                $subFilter->setFiltering($colName, $value[$alias]);
            }
        }
        return $this;
    }
}
