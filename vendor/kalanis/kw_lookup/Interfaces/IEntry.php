<?php

namespace kalanis\kw_lookup\Interfaces;


/**
 * Interface IEntry
 * @package kalanis\kw_lookup\Interfaces
 * Config entry
 */
interface IEntry
{

    /**
     * Basic entry key which will be looked up in incoming data
     * @return string
     */
    public function getKey(): string;

    /**
     * Limitation of entry
     * Entry which will be searched for limitation of operation
     * @return string
     */
    public function getLimitationKey(): string;

    /**
     * Default value of entry
     * for Filter it's Relation against value
     * possible values are constants from \Filter\Interfaces\IFilterEntry
     * for Sorter it's Direction
     * possible values are constants from \Sorter\Interfaces\ISortEntry
     * for Pager it's number
     * @return string|int
     */
    public function getDefaultLimitation();
}
