<?php

namespace kalanis\kw_paths;


/**
 * Class Stored
 * @package kalanis\kw_paths
 * Stored path data through system runtime
 */
class Stored
{
    protected static ?Path $paths = null;

    public static function init(Path $path): void
    {
        static::$paths = $path;
    }

    public static function getPath(): ?Path
    {
        return static::$paths;
    }
}
