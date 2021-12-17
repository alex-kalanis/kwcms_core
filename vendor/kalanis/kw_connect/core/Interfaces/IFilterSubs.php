<?php

namespace kalanis\kw_connect\core\Interfaces;


use kalanis\kw_connect\core\ConnectException;


/**
 * Interface IFilterSubs
 * @package kalanis\kw_connect\core\Interfaces
 * Contains filters
 */
interface IFilterSubs extends IFilterType
{
    /**
     * @param string $alias
     * @param IFilterType $filter
     * @return mixed
     * @throws ConnectException
     */
    public function addSubFilter(string $alias, IFilterType $filter): void;
}
