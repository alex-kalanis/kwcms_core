<?php

namespace kalanis\kw_connect_inputs\Configs;


use kalanis\kw_connect_inputs\Entries\FilterEntry;
use kalanis\kw_connect_inputs\Interfaces\IFilterEntries;


/**
 * Class FilterEntries
 * @package kalanis\kw_connect_inputs\Configs
 * Simple entry of filter config
 */
class FilterEntries extends AEntries implements IFilterEntries
{
    public function addEntry(FilterEntry $entry): self
    {
        $this->entries[] = $entry;
        return $this;
    }
}
