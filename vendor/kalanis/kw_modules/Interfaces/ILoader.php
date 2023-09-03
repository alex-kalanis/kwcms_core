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
     * @param string[] $module which module it will be looked for and which part in it
     * @param array<string, string|int|float|bool|object> $constructParams params passed into __construct, mainly DI
     * @throws ModuleException when module is not found
     * @return IModule|null The module or null
     */
    public function load(array $module, array $constructParams = []): ?IModule;
}
