<?php

namespace kalanis\kw_storage;


use kalanis\kw_storage\Interfaces\IStTranslations;


/**
 * Class Helper
 * @package kalanis\kw_storage
 * Create cache with already known settings
 */
class Helper
{
    public static function initStorage(?IStTranslations $lang = null): Storage
    {
        return new Storage(static::initFactory(), $lang);
    }

    public static function initFactory(): Storage\Factory
    {
        return new Storage\Factory(
            new Storage\Key\Factory(),
            new Storage\Target\Factory()
        );
    }
}
