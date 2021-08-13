<?php

namespace kalanis\kw_sorter;


use Traversable;


/**
 * Class Sorter
 * @package kalanis\kw_sorter
 * Simple sorter for determine order of result
 * No sorting inside allowed - the order IS important
 */
class Sorter implements Interfaces\ISorter
{
    /** @var Interfaces\ISortEntry[] */
    protected $entries = [];

    public function getEntries(): Traversable
    {
        yield from $this->entries;
    }

    public function add(Interfaces\ISortEntry $entry): Interfaces\ISorter
    {
        $this->entries[] = $entry;
        return $this;
    }

    public function remove(string $entryKey): Interfaces\ISorter
    {
        foreach ($this->entries as $index => $entry) {
            if ($entry->getKey() == $entryKey) {
                unset($this->entries[$index]);
            }
        }
        return $this;
    }

    public function clear(): Interfaces\ISorter
    {
        $this->entries = [];
        return $this;
    }

    public function getDefaultItem(): Interfaces\ISortEntry
    {
        return new SortByEntry();
    }
}