<?php

namespace kalanis\kw_lookup\Interfaces;


/**
 * Interface IConfig
 * @package kalanis\kw_lookup\Interfaces
 * Compact info about what is necessary to fill interfaces
 */
interface IConfig
{
    /**
     * Return settings of entries available for filtering
     * @return IFilterEntries
     */
    public function getFilterEntries(): IFilterEntries;

    /**
     * Return settings of entries available for sorting
     * @return ISorterEntries
     */
    public function getSorterEntries(): ISorterEntries;

    /**
     * Return settings of entries available for pager
     * @return IPagerEntry
     */
    public function getPagerEntries(): IPagerEntry;
}
