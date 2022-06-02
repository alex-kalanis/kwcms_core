<?php

namespace kalanis\kw_langs;


use kalanis\kw_paths\Path;
use kalanis\kw_paths\Stuff;


/**
 * Class Support
 * @package kalanis\kw_langs
 * Store translations through system runtime
 */
class Support
{
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
