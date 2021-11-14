<?php

namespace kalanis\kw_connect_doctrine\Filters;


use kalanis\kw_connect\Filters\AFilterFactory;


/**
 * Class Factory
 * @package kalanis\kw_connect_doctrine\Filters
 * Factory Class for accessing filter types
 */
class Factory extends AFilterFactory
{
    protected static $map = [
        self::ACTION_EXACT => '\kalanis\kw_connect_doctrine\Filters\Exact',
        self::ACTION_CONTAINS => '\kalanis\kw_connect_doctrine\Filters\Contains',
        self::ACTION_FROM => '\kalanis\kw_connect_doctrine\Filters\From',
        self::ACTION_FROM_WITH => '\kalanis\kw_connect_doctrine\Filters\FromWith',
        self::ACTION_TO => '\kalanis\kw_connect_doctrine\Filters\To',
        self::ACTION_TO_WITH => '\kalanis\kw_connect_doctrine\Filters\ToWith',
        self::ACTION_RANGE => '\kalanis\kw_connect_doctrine\Filters\Range',
    ];
}
