<?php

namespace KWCMS\modules\File\Lib\SizeAdapters;


/**
 * Class Factory
 * @package KWCMS\modules\File\Lib\SizeAdapters
 */
class Factory
{
    protected static $map = [
        'none' => '\KWCMS\modules\File\Lib\SizeAdapters\None',
        'seek' => '\KWCMS\modules\File\Lib\SizeAdapters\Seek',
        'bytes' => '\KWCMS\modules\File\Lib\SizeAdapters\Bytes',
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
