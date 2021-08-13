<?php

namespace kalanis\kw_sorter\Interfaces;


/**
 * Interface ISortEntry
 * @package kalanis\kw_sorter\Interfaces
 * Basic necessity to sort anything
 */
interface ISortEntry
{
    const DIRECTION_ASC = 'ASC';
    const DIRECTION_DESC = 'DESC';

    /**
     * Set by which key the entry will be sorted
     * @param string $key
     * @return $this
     */
    public function setKey(string $key): self;

    /**
     * Sort by which key
     * @return string
     */
    public function getKey(): string;

    /**
     * Set direction of sort
     * Preferably use constants above
     * @param string $direction
     * @return $this
     */
    public function setDirection(string $direction): self;

    /**
     * Sorting direction
     * Preferably use constants above
     * @return string
     */
    public function getDirection(): string;
}
