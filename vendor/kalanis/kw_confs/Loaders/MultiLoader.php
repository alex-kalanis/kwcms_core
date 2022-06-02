<?php

namespace kalanis\kw_confs\Loaders;


use kalanis\kw_confs\Interfaces\ILoader;


/**
 * Class MultiLoader
 * @package kalanis\kw_confs\Loaders
 * Load configs data from many sources
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

    public function load(string $module, string $conf = ''): ?array
    {
        foreach ($this->loaders as $loader) {
            $confs = $loader->load($module, $conf);
            if (!is_null($confs)) {
                return $confs;
            }
        }
        return null;
    }
}
