<?php

namespace kalanis\kw_connect\doctrine\Filters;


use kalanis\kw_connect\core\Filters\AFilterFactory;


/**
 * Class Factory
 * @package kalanis\kw_connect\doctrine\Filters
 * Factory Class for accessing filter types
 */
class Factory extends AFilterFactory
{
    protected static $map = [
        self::ACTION_EXACT => '\kalanis\kw_connect\doctrine\Filters\Exact',
        self::ACTION_CONTAINS => '\kalanis\kw_connect\doctrine\Filters\Contains',
        self::ACTION_FROM => '\kalanis\kw_connect\doctrine\Filters\From',
        self::ACTION_FROM_WITH => '\kalanis\kw_connect\doctrine\Filters\FromWith',
        self::ACTION_TO => '\kalanis\kw_connect\doctrine\Filters\To',
        self::ACTION_TO_WITH => '\kalanis\kw_connect\doctrine\Filters\ToWith',
        self::ACTION_RANGE => '\kalanis\kw_connect\doctrine\Filters\Range',
    ];
}
