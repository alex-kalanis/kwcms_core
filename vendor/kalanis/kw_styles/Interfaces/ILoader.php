<?php

namespace kalanis\kw_styles\Interfaces;


use kalanis\kw_styles\StylesException;


/**
 * Class ILoader
 * @package kalanis\kw_styles\Interfaces
 * Load config data from defined source
 */
interface ILoader
{
    /**
     * @param string $module which module it will be looked for
     * @param string $path which path will be looked for
     * @throws StylesException
     * @return string|null content of that source
     */
    public function load(string $module, string $path = ''): ?string;
}
