<?php

namespace KWCMS\modules\File\Lib\SizeAdapters;


use kalanis\kw_files\FilesException;
use ReflectionClass;
use ReflectionException;


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

    /**
     * @param string $unit
     * @throws FilesException
     * @return AAdapter
     */
    public static function getAdapter(string $unit): AAdapter
    {
        if (!isset(static::$map[$unit])) {
            $unit = 'none';
        }
        $class = static::$map[$unit];
        try {
            /** @var class-string $class */
            $ref = new ReflectionClass($class);
            $class = $ref->newInstance();
            if (!$class instanceof AAdapter) {
                throw new FilesException(sprintf('Class *%s* is not an instance of AAdapter', $class));
            }
            return $class;
        } catch (ReflectionException $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
