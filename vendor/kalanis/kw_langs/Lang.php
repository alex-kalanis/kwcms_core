<?php

namespace kalanis\kw_langs;


use kalanis\kw_langs\Interfaces\ILang;
use kalanis\kw_langs\Interfaces\ILoader;


/**
 * Class Lang
 * @package kalanis\kw_langs
 * Store translations through system runtime
 */
class Lang
{
    protected static ?ILoader $loader = null;
    /** @var array<string, string> */
    protected static array $translations = [];
    protected static string $usedLang = '';

    public static function init(?ILoader $loader, string $usedLang): void
    {
        static::$usedLang = $usedLang;
        static::$loader = $loader;
    }

    /**
     * @param string $module
     * @throws LangException
     */
    public static function load(string $module): void
    {
        if (!static::$loader) {
            throw new LangException('Set the loader first!');
        }
        $data = static::$loader->load($module, static::$usedLang);
        if (!empty($data)) {
            static::loadData(static::$usedLang, $data);
        }
    }

    public static function loadClass(ILang $lang): void
    {
        static::loadData(static::$usedLang, $lang->setLang(static::$usedLang)->getTranslations());
    }

    /**
     * @param string $lang
     * @param array<string, string>|array<string, array<string, string>> $translations
     */
    protected static function loadData(string $lang, array $translations): void
    {
        $translation = (isset($translations[$lang]) && is_array($translations[$lang])) ? $translations[$lang] : $translations;
        /** @var array<string, string> $translation */
        static::$translations = array_merge(static::$translations, $translation);
    }

    /**
     * @param string $key
     * @param string|int|float ...$pass
     * @return string
     */
    public static function get(string $key, ...$pass): string
    {
        $content = (isset(static::$translations[$key])) ? static::$translations[$key] : $key ;
        return strval(empty($pass) ? $content : call_user_func_array('sprintf', array_merge([$content], $pass)));
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
