<?php

namespace kalanis\kw_filter\Interfaces;


use Traversable;


/**
 * Class IFilter
 * @package kalanis\kw_filter\Interfaces
 * Composite of filters for selecting wanted items
 */
interface IFilter extends IFilterEntry
{
    const RELATION_EVERYTHING = 'and';
    const RELATION_ANYTHING = 'or';

    /**
     * Get entries in filtering
     * @return Traversable IFilterEntry
     */
    public function getEntries(): Traversable;

    /**
     * Add entry to filter
     * @param IFilterEntry $filter
     * @return $this
     */
    public function addFilter(IFilterEntry $filter): self;

    /**
     * Remove all entries which has key
     * @param string $filterKey
     * @return $this
     */
    public function remove(string $filterKey): self;

    /**
     * Clear filters
     * @return $this
     */
    public function clear(): self;

    /**
     * Return new entry usable for filtering
     * @return IFilterEntry
     */
    public function getDefaultItem(): IFilterEntry;
}
