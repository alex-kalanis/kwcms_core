<?php

namespace kalanis\kw_connect\core\Interfaces;


use kalanis\kw_connect\core\ConnectException;


/**
 * Interface IFilterFactory
 * @package kalanis\kw_connect\Interfaces
 * Which filters are available in connector
 */
interface IFilterFactory
{
    public const ACTION_EXACT = 'exact';
    public const ACTION_NOT_EXACT = 'notExact';
    public const ACTION_CONTAINS = 'contains';
    public const ACTION_FROM = 'from';
    public const ACTION_FROM_WITH = 'fromWith';
    public const ACTION_TO = 'to';
    public const ACTION_TO_WITH = 'toWith';
    public const ACTION_RANGE = 'range';
    public const ACTION_MULTIPLE = 'multiple';

    /**
     * @param string $action
     * @throws ConnectException
     * @return IFilterType
     */
    public function getFilter(string $action): IFilterType;
}
