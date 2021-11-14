<?php

namespace kalanis\kw_connect_dibi\Filters;


use kalanis\kw_connect\Filters\AFilterFactory;


/**
 * Class Factory
 * @package kalanis\kw_connect_dibi\Filters
 * Factory Class for accessing filter types
 */
class Factory extends AFilterFactory
{
    protected static $map = [
        self::ACTION_EXACT => '\kalanis\kw_connect_dibi\Filters\Exact',
        self::ACTION_CONTAINS => '\kalanis\kw_connect_dibi\Filters\Contains',
        self::ACTION_FROM => '\kalanis\kw_connect_dibi\Filters\From',
        self::ACTION_FROM_WITH => '\kalanis\kw_connect_dibi\Filters\FromWith',
        self::ACTION_TO => '\kalanis\kw_connect_dibi\Filters\To',
        self::ACTION_TO_WITH => '\kalanis\kw_connect_dibi\Filters\ToWith',
        self::ACTION_RANGE => '\kalanis\kw_connect_dibi\Filters\Range',
    ];
}
