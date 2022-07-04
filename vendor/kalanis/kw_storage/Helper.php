<?php

namespace kalanis\kw_storage;


/**
 * Class Helper
 * @package kalanis\kw_storage
 * Create cache with already known settings
 */
class Helper
{
    public static function initStorage(): Storage
    {
        return new Storage(static::initFactory());
    }

    public static function initFactory(): Storage\Factory
    {
        return new Storage\Factory(
            new Storage\Key\Factory(),
            new Storage\Target\Factory()
        );
    }
}
