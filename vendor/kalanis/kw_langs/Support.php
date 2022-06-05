<?php

namespace kalanis\kw_langs;


use ArrayAccess;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\Stuff;


/**
 * Class Support
 * @package kalanis\kw_langs
 * Store translations through system runtime
 */
class Support
{
    const LANG_KEY = 'lang';

    public static function fillFromArray(ArrayAccess $array, ?string $defaultLang): ?string
    {
        return $array->offsetExists(static::LANG_KEY)
            && !empty($array->offsetGet(static::LANG_KEY))
            && is_string($array->offsetGet(static::LANG_KEY))
            ? $array->offsetGet(static::LANG_KEY)
            : $defaultLang ;
    }

    public static function setToArray(ArrayAccess $array, string $lang): void
    {
        $array->offsetSet(static::LANG_KEY, $lang);
    }

    public static function fillFromPaths(Path $path, string $defaultLang, bool $moreLangs): string
    {
        if ($path->getLang()) {
            return $path->getLang();
        }
        if ($moreLangs && !empty($path->getPath())) {
            $trace = Stuff::pathToArray($path->getPath());
            $firstDir = reset($trace);
            $length = strlen($firstDir);
            if (1 < $length && 4 > $length) { // two-letter "en", three letter "eng"
                return $firstDir;
            }
        }
        return $defaultLang;
    }
}
