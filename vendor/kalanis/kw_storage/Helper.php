<?php

namespace kalanis\kw_storage;


use kalanis\kw_storage\Interfaces\IStorage;
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

    /**
     * @param array<string|int, string|int|float|object|bool|array<string|int|float|object>>|string|object|int|bool|null $params
     * @throws StorageException
     * @return IStorage
     */
    public static function getStorage($params): IStorage
    {
        return (new Access\Factory())->getStorage($params);
    }

    /**
     * @param array<string|int, string|int|float|object|bool|array<string|int|float|object>>|string|object|int|bool|null $params
     * @param string|null $alias
     * @throws StorageException
     * @return IStorage
     */
    public static function getMultiStorage($params, ?string $alias = null): IStorage
    {
        return Access\MultitonInstances::getInstance()->lookup($params, $alias);
    }
}
