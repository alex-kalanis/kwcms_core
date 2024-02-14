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
     * @param string $delimiter
     * @throws PathsException
     * @return string
     */
    public static function sanitize(string $path, string $delimiter = DIRECTORY_SEPARATOR): string
    {
        return static::arrayToPath(array_filter(array_filter(static::pathToArray($path, $delimiter), [Stuff::class, 'notDots'])), $delimiter);
    }

    public static function notDots(string $content): bool
    {
        return !in_array($content, ['.', '..']);
    }

    /**
     * @param string $path
     * @param string $delimiter
     * @throws PathsException
     * @return array<int, string>
     */
    public static function pathToArray(string $path, string $delimiter = DIRECTORY_SEPARATOR): array
    {
        return Extras\PathTransform::get()->expandName($path, $delimiter);
    }

    /**
     * @param string[] $path
     * @param string $delimiter
     * @throws PathsException
     * @return string
     */
    public static function arrayToPath(array $path, string $delimiter = DIRECTORY_SEPARATOR): string
    {
        return Extras\PathTransform::get()->compactName($path, $delimiter);
    }

    /**
     * @param string $path
     * @param string $delimiter
     * @throws PathsException
     * @return array<int, string>
     */
    public static function linkToArray(string $path, string $delimiter = IPaths::SPLITTER_SLASH): array
    {
        return Extras\PathTransform::get()->expandName($path, $delimiter);
    }

    /**
     * @param string[] $path
     * @param string $delimiter
     * @throws PathsException
     * @return string
     */
    public static function arrayToLink(array $path, string $delimiter = IPaths::SPLITTER_SLASH): string
    {
        return Extras\PathTransform::get()->compactName($path, $delimiter);
    }

    /**
     * Path to file (with trailing slash)
     * @param string $path
     * @param string $delimiter
     * @return string
     * Do not use dirname(), because has problems with code pages
     */
    public static function directory(string $path, string $delimiter = DIRECTORY_SEPARATOR): string
    {
        $pos = mb_strrpos($path, $delimiter);
        return (false !== $pos) ? mb_substr($path, 0, $pos + 1) : '' ;
    }

    /**
     * Name of file from the whole path
     * @param string $path
     * @param string $delimiter
     * @return string
     * Do not use basename(), because has problems with code pages
     */
    public static function filename(string $path, string $delimiter = DIRECTORY_SEPARATOR): string
    {
        $pos = mb_strrpos($path, $delimiter);
        return (false !== $pos) ? mb_substr($path, $pos + 1) : $path ;
    }

    /**
     * Base of file (part before the "dot")
     * @param string $path
     * @param string $delimiter
     * @return string
     */
    public static function fileBase(string $path, string $delimiter = IPaths::SPLITTER_DOT): string
    {
        $pos = mb_strrpos($path, $delimiter);
        return (false !== $pos) && (0 < $pos) ? mb_substr($path, 0, $pos) : $path ;
    }

    /**
     * Extension of file (part after the "dot" if it exists)
     * @param string $path
     * @param string $delimiter
     * @return string
     */
    public static function fileExt(string $path, string $delimiter = IPaths::SPLITTER_DOT): string
    {
        $pos = mb_strrpos($path, $delimiter);
        return ((false !== $pos) && (0 < $pos)) ? mb_substr($path, $pos + 1) : '' ;
    }

    /**
     * Remove ending slash
     * @param string $path
     * @param string $delimiter
     * @return string
     */
    public static function removeEndingSlash(string $path, string $delimiter = DIRECTORY_SEPARATOR): string
    {
        return ($delimiter == mb_substr($path, -1, 1)) ? mb_substr($path, 0, -1) : $path ;
    }

    /**
     * Return correct name with no non-ascii characters
     * @param string $name
     * @param int $maxLen
     * @param string $delimiter
     * @return string
     */
    public static function canonize(string $name, int $maxLen = 127, string $delimiter = IPaths::SPLITTER_DOT): string
    {
        $fName = preg_replace('/((&[[:alpha:]]{1,6};)|(&#[[:alnum:]]{1,7};))/', '', $name); // remove ascii-escaped chars
        $fName = preg_replace('/[^[:alnum:]_\s\-\.]/', '', strval($fName)); // remove non-alnum + dots
        $fName = preg_replace('/[\s]/', '_', strval($fName)); // whitespaces to underscore
        $fName = strval($fName);
        $ext = static::fileExt($fName);
        $base = static::fileBase($fName);
        $extLen = strlen($ext);
        $cut = substr($base, 0, ($maxLen - $extLen));
        return ($extLen) ? $cut . $delimiter . $ext : $cut ;
    }

    /**
     * @param string $param
     * @return array<string|int, string|int|float|bool|array<string|int>>
     */
    public static function httpStringIntoArray(string $param): array
    {
        parse_str(html_entity_decode($param, ENT_QUOTES | ENT_HTML5, 'UTF-8'), $result);
        return $result;
    }

    /**
     * @param array<string|int, string|int|float|bool|array<string|int>> $param
     * @return string
     */
    public static function arrayIntoHttpString(array $param): string
    {
        return http_build_query($param);
    }
}
