<?php

namespace kalanis\kw_connect\core\Filters\Arrays;


use kalanis\kw_connect\core\Filters\AFilterFactory;


/**
 * Class ArrayFactory
 * @package kalanis\kw_connect\Filters\Arrays
 * Factory Class for accessing filter types
 */
class Factory extends AFilterFactory
{
    protected static $map = [
        self::ACTION_CONTAINS => '\kalanis\kw_connect\core\Filters\Arrays\Contains',
        self::ACTION_EXACT => '\kalanis\kw_connect\core\Filters\Arrays\Exact',
        self::ACTION_FROM => '\kalanis\kw_connect\core\Filters\Arrays\From',
        self::ACTION_FROM_WITH => '\kalanis\kw_connect\core\Filters\Arrays\FromWith',
        self::ACTION_TO => '\kalanis\kw_connect\core\Filters\Arrays\To',
        self::ACTION_TO_WITH => '\kalanis\kw_connect\core\Filters\Arrays\ToWith',
        self::ACTION_RANGE => '\kalanis\kw_connect\core\Filters\Arrays\Range',
    ];
}
