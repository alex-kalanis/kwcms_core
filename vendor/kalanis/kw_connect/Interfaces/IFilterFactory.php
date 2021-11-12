<?php

namespace kalanis\kw_connect\Interfaces;


use kalanis\kw_connect\ConnectException;


/**
 * Interface IFilterFactory
 * @package kalanis\kw_connect\Interfaces
 * Which filters are available in connector
 */
interface IFilterFactory
{
    const ACTION_EXACT = 'exact';
    const ACTION_NOT_EXACT = 'notExact';
    const ACTION_CONTAINS = 'contains';
    const ACTION_FROM = 'from';
    const ACTION_FROM_WITH = 'fromWith';
    const ACTION_TO = 'to';
    const ACTION_TO_WITH = 'toWith';
    const ACTION_RANGE = 'range';

    /**
     * @param string $action
     * @return IFilterType
     * @throws ConnectException
     */
    public function getFilter(string $action): IFilterType;
}
