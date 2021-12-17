<?php

namespace kalanis\kw_connect\core\Filters\Arrays;


use kalanis\kw_connect\core\Interfaces\IFilterSubs;
use kalanis\kw_connect\core\Interfaces\IFilterType;


/**
 * Class Multiple
 * @package kalanis\kw_connect\core\Filters\Arrays
 * Multiple filters behaves as one for that column
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
        $data = $this->dataSource;
        foreach ($this->subFilters as $alias => &$subFilter) {
            if (isset($value[$alias]) && (IFilterType::EMPTY_FILTER != $value[$alias])) {
                $subFilter->setDataSource($data);
                $subFilter->setFiltering($colName, $value[$alias]);
                $data = $subFilter->getDataSource();
            }
        }
        $this->dataSource = $data;
        return $this;
    }
}
