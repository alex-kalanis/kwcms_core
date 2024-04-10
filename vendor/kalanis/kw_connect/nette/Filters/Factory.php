<?php

namespace kalanis\kw_connect\nette\Filters;


use kalanis\kw_connect\core\AFilterFactory;


/**
 * Class Factory
 * @package kalanis\kw_connect\nette\Filters
 * Factory Class for accessing filter types
 */
class Factory extends AFilterFactory
{
    protected static array $map = [
        self::ACTION_EXACT => Exact::class,
        self::ACTION_CONTAINS => Contains::class,
        self::ACTION_FROM => From::class,
        self::ACTION_FROM_WITH => FromWith::class,
        self::ACTION_TO => To::class,
        self::ACTION_TO_WITH => ToWith::class,
        self::ACTION_RANGE => Range::class,
        self::ACTION_MULTIPLE => Multiple::class,
    ];
}
