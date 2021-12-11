<?php

namespace kalanis\kw_lookup\Configs;


use kalanis\kw_lookup\Entries\SorterEntry;
use kalanis\kw_lookup\Interfaces\ISorterEntries;


/**
 * Class SorterEntries
 * @package kalanis\kw_lookup\Configs
 * Simple entry of sorter config
 */
class SorterEntries extends AEntries implements ISorterEntries
{
    public function addEntry(SorterEntry $entry): self
    {
        $this->entries[] = $entry;
        return $this;
    }
}
