<?php

namespace kalanis\kw_scripts\Interfaces;


use kalanis\kw_scripts\ScriptsException;


/**
 * Class ILoader
 * @package kalanis\kw_scripts\Interfaces
 * Load config data from defined source
 */
interface ILoader
{
    /**
     * @param string $module which module it will be looked for
     * @param string $path which path will be looked for
     * @throws ScriptsException
     * @return string|null content of that source, null if not found
     */
    public function load(string $module, string $path = ''): ?string;
}
