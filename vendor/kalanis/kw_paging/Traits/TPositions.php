<?php

namespace kalanis\kw_paging\Traits;


use kalanis\kw_paging\Interfaces\IPositions;


/**
 * Trait TPositions
 * @package kalanis\kw_paging\Render\SimplifiedPager
 * Trait for accessing positions
 */
trait TPositions
{
    protected ?IPositions $positions = null;

    public function getPositions(): IPositions
    {
        if (empty($this->positions)) {
            throw new \LogicException('Set positions first!');
        }
        return $this->positions;
    }
}
