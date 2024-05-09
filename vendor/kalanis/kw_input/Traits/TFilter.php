<?php

namespace kalanis\kw_input\Traits;


use kalanis\kw_input\Interfaces;


/**
 * Trait TFilter
 * @package kalanis\kw_input\Traits
 * Filter inputs
 */
trait TFilter
{
    /**
     * @param string|null $entryKey
     * @param Interfaces\IEntry[] $availableEntries
     * @return Interfaces\IEntry[]
     */
    protected function whichKeys(?string $entryKey, array $availableEntries): array
    {
        return !is_null($entryKey)
            ? $this->filteredByKeys($entryKey, $availableEntries)
            : $availableEntries
        ;
    }

    /**
     * @param string[] $entrySources
     * @param Interfaces\IEntry[] $entriesWithKeys
     * @return Interfaces\IEntry[]
     */
    protected function whichSource(array $entrySources, array $entriesWithKeys): array
    {
        return !empty($entrySources)
            ? $this->filteredBySources($entrySources, $entriesWithKeys)
            : $entriesWithKeys
        ;
    }

    /**
     * @param string $entryKey
     * @param Interfaces\IEntry[] $availableEntries
     * @return Interfaces\IEntry[]
     */
    protected function filteredByKeys(string &$entryKey, array &$availableEntries): array
    {
        $entriesWithKeys = [];
        foreach ($availableEntries as &$availableEntry) {
            if ($availableEntry->getKey() == $entryKey) {
                $entriesWithKeys[] = $availableEntry;
            }
        }
        return $entriesWithKeys;
    }

    /**
     * @param string[] $sources
     * @param Interfaces\IEntry[] $entriesWithKeys
     * @return Interfaces\IEntry[]
     */
    protected function filteredBySources(array &$sources, array &$entriesWithKeys): array
    {
        $passedEntries = [];
        foreach ($sources as $source) {
            foreach ($entriesWithKeys as $entryWithKey) {
                if ($entryWithKey->getSource() == $source) {
                    $passedEntries[$entryWithKey->getKey()] = $entryWithKey;
                }
            }
        }
        return array_values($passedEntries);
    }
}
