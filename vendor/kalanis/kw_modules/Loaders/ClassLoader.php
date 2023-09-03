<?php

namespace kalanis\kw_modules\Loaders;


use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\IModule;


/**
 * Class ClassLoader
 * @package kalanis\kw_modules\Loaders
 * Load modules data from different sources
 */
class ClassLoader implements ILoader
{
    /** @var ILoader[] */
    protected $loaders = [];

    /**
     * @param ILoader[] $loaders
     */
    public function __construct(array $loaders)
    {
        $this->loaders = $loaders;
    }

    public function load(array $module, array $constructParams = []): ?IModule
    {
        foreach ($this->loaders as $loader) {
            $got = $loader->load($module, $constructParams);
            if ($got) {
                return $got;
            }
        }
        return null;
    }
}
