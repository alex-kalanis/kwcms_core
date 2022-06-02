<?php

namespace kalanis\kw_scripts\Loaders;


use kalanis\kw_scripts\Interfaces\ILoader;


/**
 * Class MultiLoader
 * @package kalanis\kw_scripts\Loaders
 * Load script data from many sources
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
