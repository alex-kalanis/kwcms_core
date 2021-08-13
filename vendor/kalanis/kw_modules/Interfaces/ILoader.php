<?php

namespace kalanis\kw_modules\Interfaces;


use kalanis\kw_modules\ModuleException;


/**
 * Class ILoader
 * @package kalanis\kw_modules\Interfaces
 * Load translation data from defined source
 */
interface ILoader
{
    /**
     * @param string $module which module it will be looked for
     * @param string|null $constructPath next parts in target
     * @param array $constructParams params passed into __construct, mainly DI
     * @return IModule The module
     * @throws ModuleException when module is not found
     */
    public function load(string $module, ?string $constructPath = null, array $constructParams = []): IModule;
}
