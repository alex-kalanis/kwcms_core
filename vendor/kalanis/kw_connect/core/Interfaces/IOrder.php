<?php

namespace kalanis\kw_connect\core\Interfaces;


/**
 * Interface IOrder
 * @package kalanis\kw_connect\core\Interfaces
 * Just constants for ordering
 * Might be the same as in kw_mapper/IQueryBuilder and other data sources
 * But be ready to transform this one to Eval from php8
 */
interface IOrder
{
    public const ORDER_ASC = 'ASC';
    public const ORDER_DESC = 'DESC';
}
