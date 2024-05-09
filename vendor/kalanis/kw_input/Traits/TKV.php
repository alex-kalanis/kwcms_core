<?php

namespace kalanis\kw_input\Traits;


use kalanis\kw_input\Entries\Entry;
use kalanis\kw_input\Interfaces;


/**
 * Trait TKV
 * @package kalanis\kw_input\Traits
 * Keys-values pairs from inputs
 */
trait TKV
{
    /**
     * @param Interfaces\IEntry[] $entries
     * @return array<string, Interfaces\IEntry>
     */
    protected function keysValues(array $entries): array
    {
        $results = [];
        foreach ($entries as &$entry) {
            $results[strval($entry->getKey())] = $entry;
        }
        return $results;
    }
}
