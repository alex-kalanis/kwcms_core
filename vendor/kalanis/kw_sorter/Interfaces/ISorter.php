<?php

namespace kalanis\kw_sorter\Interfaces;


use Traversable;


/**
 * Interface ISorter
 * @package kalanis\kw_sorter\Interfaces
 * Basic interface
 * Make your app dependant on this interface
 */
interface ISorter
{
    /**
     * Get entries in sorting
     * @return Traversable ISortEntry
     */
    public function getEntries(): Traversable;

    /**
     * Add single entry from input which will be used for sorting
     * @param ISortEntry $entry
     * @return $this
     */
    public function add(ISortEntry $entry): self;

    /**
     * Remove all entries containing that key
     * @param string $entryKey
     * @return $this
     */
    public function remove(string $entryKey): self;

    /**
     * Clear sorting entries, be ready for another set
     * @return $this
     */
    public function clear(): self;

    /**
     * Return new entry usable for sorting
     * @return ISortEntry
     */
    public function getDefaultItem(): ISortEntry;
}
