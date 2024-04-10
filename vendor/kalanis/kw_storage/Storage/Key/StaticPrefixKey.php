<?php

namespace kalanis\kw_storage\Storage\Key;


use kalanis\kw_storage\Interfaces\IKey;


/**
 * Class StaticPrefixKey
 * @package kalanis\kw_storage\Storage\Key
 * The key is part of a directory path - fill it
 */
class StaticPrefixKey implements IKey
{
    protected static string $prefix = '/var/cache/wwwcache/';

    public static function setPrefix(string $prefix): void
    {
        static::$prefix = $prefix;
    }

    /**
     * @param string $key channel Id
     * @return string
     * /var/cache/wwwcache - coming from cache check
     */
    public function fromSharedKey(string $key): string
    {
        return static::$prefix . $key;
    }
}
