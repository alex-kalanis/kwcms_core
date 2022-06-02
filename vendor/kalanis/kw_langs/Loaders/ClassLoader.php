<?php

namespace kalanis\kw_langs\Loaders;


use kalanis\kw_langs\Interfaces\ILang;
use kalanis\kw_langs\Interfaces\ILoader;


/**
 * Class ClassLoader
 * @package kalanis\kw_langs
 * Load lang data from class
 */
class ClassLoader implements ILoader
{
    /** @var ILang[] */
    protected $langs = [];

    public function addClass(ILang $lang): self
    {
        $this->langs[get_class($lang)] = $lang;
        return $this;
    }

    public function load(string $module, string $lang): ?array
    {
        foreach ($this->langs as $langClassName => $langClass) {
            if ($langClassName == $module) {
                $langs = $langClass->setLang($lang)->getTranslations();
                if (!empty($langs)) {
                    return $langs;
                }
            }
        }
        return null;
    }
}
