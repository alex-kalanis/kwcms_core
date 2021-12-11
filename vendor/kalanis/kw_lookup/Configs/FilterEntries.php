<?php

namespace kalanis\kw_lookup\Configs;


use kalanis\kw_lookup\Entries\FilterEntry;
use kalanis\kw_lookup\Interfaces\IFilterEntries;


/**
 * Class FilterEntries
 * @package kalanis\kw_lookup\Configs
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
