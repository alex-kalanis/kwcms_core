<?php

namespace KWCMS\modules\File\Lib\SizeAdapters;


/**
 * Class Factory
 * @package KWCMS\modules\File\Lib\SizeAdapters
 */
class Factory
{
    protected static $map = [
        'none' => None::class,
        'seek' => Seek::class,
        'bytes' => Bytes::class,
    ];

    public static function getAdapter(string $unit): AAdapter
    {
        if (!isset(static::$map[$unit])) {
            $unit = 'none';
        }
        $class = static::$map[$unit];
        return new $class();
    }
}
