<?php

namespace kalanis\kw_connect_inputs\Entries;


use kalanis\kw_sorter\Interfaces\ISortEntry;


/**
 * Class SorterEntry
 * @package kalanis\kw_connect_inputs\Entries
 * Simple entry of sorter config - just what entry is important for sorting
 */
class SorterEntry extends AEntry
{
    protected static $defaultLimits = [
        ISortEntry::DIRECTION_DESC,
        ISortEntry::DIRECTION_ASC,
    ];

    public function setEntry(string $key, string $limitationKey, string $defaultLimit): self
    {
        $this->key = $key;
        $this->limitationKey = $limitationKey;
        $this->defaultLimit = $this->defaultLimits($defaultLimit);
        return $this;
    }

    protected function defaultLimits(string $source): string
    {
        return in_array($source, static::$defaultLimits) ? $source : $this->defaultLimit;
    }
}
