<?php

namespace kalanis\kw_paging\Traits;


use kalanis\kw_paging\Interfaces\IPositions;


/**
 * Trait THelpingText
 * @package kalanis\kw_paging\Render\SimplifiedPager
 * Trait for render simple helping text about
 */
trait THelpingText
{
    use TLang;

    public function getFilledText(?IPositions $positions): string
    {
        if (!$this->kpgLang || !$positions) {
            return '';
        }
        return $this->getKpgLang()->kpgShowResults(
            $positions->getPager()->getOffset() + 1,
            min($positions->getPager()->getOffset() + $positions->getPager()->getLimit(), $positions->getPager()->getMaxResults()),
            $positions->getPager()->getMaxResults()
        );
    }
}
