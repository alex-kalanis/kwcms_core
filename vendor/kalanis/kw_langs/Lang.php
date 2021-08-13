<?php

namespace kalanis\kw_langs;


use kalanis\kw_langs\Interfaces\ILang;
use kalanis\kw_langs\Interfaces\ILoader;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\Stuff;


/**
 * Class Lang
 * @package kalanis\kw_langs
 * Store translations through system runtime
 */
class Lang
{
    /** @var ILoader */
    protected static $loader = null;
    /** @var string[][] */
    protected static $translations = [];
    /** @var string */
    protected static $usedLang = '';

    public static function init(Path $path, string $defaultLang, ?ILoader $loader = null, bool $moreLangs = false): void
    {
        if (empty($loader)) {
            $loader = new Loaders\PhpLoader();
            $loader->setPathLib($path);
        }
        static::$usedLang = static::fillFromPaths($path, $defaultLang, $moreLangs);
        static::$loader = $loader;
    }

    protected static function fillFromPaths(Path $path, string $defaultLang, bool $moreLangs): string
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

    public static function load(string $module): void
    {
        static::loadData(static::$usedLang, static::$loader->load($module, static::$usedLang));
    }

    public static function loadClass(ILang $lang): void
    {
        static::loadData(static::$usedLang, $lang->getTranslations());
    }

    protected static function loadData(string $lang, array $translations): void
    {
        $translations = (isset($translations[$lang]) && is_array($translations[$lang])) ? $translations[$lang] : $translations;
        static::$translations = array_merge(static::$translations, $translations);
    }

    public static function get(string $key, ...$pass): string
    {
        $content = (isset(static::$translations[$key])) ? static::$translations[$key] : $key ;
        return call_user_func_array('sprintf', array_merge([$content], $pass));
    }

    public static function getLang(): string
    {
        return static::$usedLang;
    }

    public static function getLoader(): ?ILoader
    {
        return static::$loader;
    }
}
