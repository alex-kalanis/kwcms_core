<?php

namespace kalanis\kw_paging\Render;


use kalanis\kw_paging\Interfaces\IPositions;


/**
 * Trait THelpingText
 * @package kalanis\kw_paging\Render\SimplifiedPager
 * Trait for render simple helping text about
 */
trait THelpingText
{
    protected function getHelpingText(): string
    {
        return 'Showing results %d - %d of total %d';
    }

    public function getFilledText(IPositions $positions): string
    {
        return sprintf(
            $this->getHelpingText(),
            $positions->getPager()->getOffset() + 1,
            min($positions->getPager()->getOffset() + $positions->getPager()->getLimit(), $positions->getPager()->getMaxResults()),
            $positions->getPager()->getMaxResults()
        );
    }
}
