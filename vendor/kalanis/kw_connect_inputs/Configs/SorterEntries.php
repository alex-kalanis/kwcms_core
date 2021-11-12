<?php

namespace kalanis\kw_connect_inputs\Configs;


use kalanis\kw_connect_inputs\Entries\SorterEntry;
use kalanis\kw_connect_inputs\Interfaces\ISorterEntries;


/**
 * Class SorterEntries
 * @package kalanis\kw_connect_inputs\Configs
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
