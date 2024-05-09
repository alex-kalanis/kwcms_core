<?php

namespace kalanis\kw_routed_paths;


/**
 * Class StoreRouted
 * @package kalanis\kw_routed_paths
 * Stored path data through system runtime
 */
class StoreRouted
{
    protected static ?RoutedPath $paths = null;

    public static function init(RoutedPath $path): void
    {
        static::$paths = $path;
    }

    public static function getPath(): ?RoutedPath
    {
        return static::$paths;
    }
}
