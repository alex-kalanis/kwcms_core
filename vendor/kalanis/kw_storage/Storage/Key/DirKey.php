<?php

namespace kalanis\kw_storage\Storage\Key;


use kalanis\kw_storage\Interfaces\IKey;


/**
 * Class DirKey
 * @package kalanis\kw_storage\Storage\Key
 * The key is part of a directory path - fill it
 */
class DirKey implements IKey
{
    /** @var string */
    protected static $dir= '/var/cache/wwwcache/';

    public static function setDir(string $dir): void
    {
        static::$dir = $dir;
    }

    /**
     * @param string $key channel Id
     * @return string
     * /var/cache/wwwcache - coming from cache check
     */
    public function fromSharedKey(string $key): string
    {
        return static::$dir . $key;
    }
}
