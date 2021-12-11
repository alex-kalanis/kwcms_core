<?php

namespace kalanis\kw_connect\Configs;


use kalanis\kw_lookup\Interfaces;


/**
 * Class Config
 * @package kalanis\kw_lookup\Configs
 * Whole configuration package
 */
class Config implements Interfaces\IConfig
{
    /** @var Interfaces\IFilterEntries */
    protected $filterEntries = null;
    /** @var Interfaces\ISorterEntries */
    protected $sorterEntries = null;
    /** @var Interfaces\IPagerEntry */
    protected $pagerEntry = null;

    public function __construct(Interfaces\IFilterEntries $filterEntries, Interfaces\ISorterEntries $sorterEntries, Interfaces\IPagerEntry $pagerEntry)
    {
        $this->filterEntries = $filterEntries;
        $this->sorterEntries = $sorterEntries;
        $this->pagerEntry = $pagerEntry;
    }

    public function getFilterEntries(): Interfaces\IFilterEntries
    {
        return $this->filterEntries;
    }

    public function getSorterEntries(): Interfaces\ISorterEntries
    {
        return $this->sorterEntries;
    }

    public function getPagerEntries(): Interfaces\IPagerEntry
    {
        return $this->pagerEntry;
    }
}
