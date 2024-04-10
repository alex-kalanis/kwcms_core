<?php

namespace kalanis\kw_input\Interfaces;


/**
 * Interface IVariables
 * @package kalanis\kw_input
 * Helper interface which allows us access variables from input
 */
interface IFiltered
{
    /**
     * Reformat into array with key as array key and value with the whole entry
     * @param string|null $entryKey
     * @param string[] $entrySources
     * @return array<string, IEntry>
     * Also usually came in pair with previous call - but with a different syntax
     * Beware - due any dict limitations there is a limitation that only the last entry prevails
     *
     * $entries = $variables->getInArray('example', [Entries\IEntry::SOURCE_GET]);
     */
    public function getInArray(?string $entryKey = null, array $entrySources = []): array;
}
