<?php

namespace kalanis\kw_langs\Loaders;


use kalanis\kw_langs\Interfaces\ILoader;


/**
 * Class MultiLoader
 * @package kalanis\kw_langs
 * Load lang data from many sources
 */
class MultiLoader implements ILoader
{
    /** @var ILoader[] */
    protected $loaders = [];

    public function addLoader(ILoader $loader): self
    {
        $this->loaders[get_class($loader)] = $loader;
        return $this;
    }

    public function load(string $module, string $lang): ?array
    {
        foreach ($this->loaders as $loader) {
            $langs = $loader->load($module, $lang);
            if (!is_null($langs)) {
                return $langs;
            }
        }
        return null;
    }
}
