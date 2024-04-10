<?php

namespace kalanis\kw_langs;


use ArrayAccess;
use kalanis\kw_routed_paths\RoutedPath;


/**
 * Class Support
 * @package kalanis\kw_langs
 * Store translations through system runtime
 */
class Support
{
    public const LANG_KEY = 'lang';

    /**
     * @param ArrayAccess<string, mixed> $array
     * @param string|null $defaultLang
     * @return string|null
     */
    public static function fillFromArray(ArrayAccess $array, ?string $defaultLang): ?string
    {
        return $array->offsetExists(static::LANG_KEY)
        && !empty($array->offsetGet(static::LANG_KEY))
        && is_string($array->offsetGet(static::LANG_KEY))
            ? $array->offsetGet(static::LANG_KEY)
            : $defaultLang ;
    }

    /**
     * @param ArrayAccess<string, string> $array
     * @param string $lang
     */
    public static function setToArray(ArrayAccess $array, string $lang): void
    {
        $array->offsetSet(static::LANG_KEY, $lang);
    }

    public static function fillFromPaths(RoutedPath $path, string $defaultLang, bool $moreLangs): string
    {
        if ($path->getLang()) {
            return $path->getLang();
        }
        if ($moreLangs && !empty($path->getPath())) {
            $trace = $path->getPath();
            $firstDir = reset($trace);
            if (false !== $firstDir) {
                $length = strlen($firstDir);
                if ((1 < $length) && (4 > $length)) { // two-letter "en", three letter "eng"
                    return $firstDir;
                }
            }
        }
        return $defaultLang;
    }
}
