<?php

namespace kalanis\kw_styles\Loaders;


use kalanis\kw_styles\Interfaces\ILoader;


/**
 * Class MultiLoader
 * @package kalanis\kw_styles\Loaders
 * Load style data from many sources
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

    public function load(string $module, string $path = ''): ?string
    {
        foreach ($this->loaders as $loader) {
            $style = $loader->load($module, $path);
            if (!is_null($style)) {
                return $style;
            }
        }
        return null;
    }
}
