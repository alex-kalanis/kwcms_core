<?php

namespace kalanis\kw_input\Loaders;


use kalanis\kw_input\Entries;


/**
 * Class ALoader
 * @package kalanis\kw_input\Loaders
 * Load input arrays into normalized entries
 */
abstract class ALoader
{
    /**
     * Transform input values to something more reliable
     * @param string $source
     * @param array $array
     * @return Entries\Entry[]
     */
    abstract public function loadVars(string $source, &$array): array;
}
