<?php

namespace kalanis\kw_paths;


use kalanis\kw_paths\Interfaces\IPaths;


/**
 * Class Stuff
 * @package kalanis\kw_paths
 * Stuff helping parsing the paths
 * Do not use pathinfo(), because has problems with code pages
 */
class Stuff
{
    /**
     * Return path with no extra dots and slashes
     * Do not call realpath() which do the similar thing
     * @param string $path
     * @return string
     */
    public static function sanitize(string $path): string
    {
        return static::arrayToPath(array_filter(array_filter(static::pathToArray($path), ['\kalanis\kw_paths\Stuff', 'notDots'])));
    }

    public static function notDots($content): bool
    {
        return !in_array($content, ['.', '..']);
    }

    public static function pathToArray(string $path): array
    {
        return explode(DIRECTORY_SEPARATOR, $path); // OS dependent
    }

    public static function arrayToPath(array $path): string
    {
        return implode(DIRECTORY_SEPARATOR, $path); // OS dependent
    }

    public static function linkToArray(string $path): array
    {
        return explode(IPaths::SPLITTER_SLASH, $path); // HTTP dependent
    }

    public static function arrayToLink(array $path): string
    {
        return implode(IPaths::SPLITTER_SLASH, $path); // HTTP dependent
    }

    /**
     * Path to file (with trailing slash)
     * @param string $path
     * @return string
     * Do not use dirname(), because has problems with code pages
     */
    public static function directory(string $path): string
    {
        $pos = mb_strrpos($path, DIRECTORY_SEPARATOR);
        return ($pos !== false) ? mb_substr($path, 0, $pos + 1) : '' ;
    }

    /**
     * Name of file from the whole path
     * @param string $path
     * @return string
     * Do not use basename(), because has problems with code pages
     */
    public static function filename(string $path): string
    {
        $pos = mb_strrpos($path, DIRECTORY_SEPARATOR);
        return ($pos !== false) ? mb_substr($path, $pos + 1) : $path ;
    }

    /**
     * Base of file (part before the "dot")
     * @param string $path
     * @return string
     */
    public static function fileBase(string $path): string
    {
        $pos = mb_strrpos($path, IPaths::SPLITTER_DOT);
        return ($pos !== false) && ($pos > 0) ? mb_substr($path, 0, $pos) : $path ;
    }

    /**
     * Extension of file (part after the "dot" if it exists)
     * @param string $path
     * @return string
     */
    public static function fileExt(string $path): string
    {
        $pos = mb_strrpos($path, IPaths::SPLITTER_DOT);
        return (($pos !== false) && ($pos > 0)) ? mb_substr($path, $pos + 1) : '' ;
    }

    /**
     * Remove ending slash
     * @param string $path
     * @return string
     */
    public static function removeEndingSlash(string $path): string
    {
        return (mb_substr($path, -1, 1) == DIRECTORY_SEPARATOR) ? mb_substr($path, 0, -1) : $path ;
    }
}
