<?php

namespace kalanis\kw_connect\Filters\Arrays;


use kalanis\kw_connect\Filters\AFilterFactory;


/**
 * Class ArrayFactory
 * @package kalanis\kw_connect\Filters\Arrays
 * Factory Class for accessing filter types
 */
class Factory extends AFilterFactory
{
    protected static $map = [
        self::ACTION_CONTAINS => '\kalanis\kw_connect\Filters\Arrays\Contains',
        self::ACTION_EXACT => '\kalanis\kw_connect\Filters\Arrays\Exact',
        self::ACTION_FROM => '\kalanis\kw_connect\Filters\Arrays\From',
        self::ACTION_FROM_WITH => '\kalanis\kw_connect\Filters\Arrays\FromWith',
        self::ACTION_TO => '\kalanis\kw_connect\Filters\Arrays\To',
        self::ACTION_TO_WITH => '\kalanis\kw_connect\Filters\Arrays\ToWith',
        self::ACTION_RANGE => '\kalanis\kw_connect\Filters\Arrays\Range',
    ];
}
