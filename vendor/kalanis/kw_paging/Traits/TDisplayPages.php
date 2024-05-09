<?php

namespace kalanis\kw_paging\Traits;


use kalanis\kw_paging\Interfaces\IPositions;


/**
 * Trait TDisplayPages
 * @package kalanis\kw_paging\Render
 * Trait to select pages to render
 */
trait TDisplayPages
{
    use TPositions;

    protected int $displayPagesCount = IPositions::DEFAULT_DISPLAY_PAGES_COUNT;

    /**
     * Return array of page numbers, which will be rendered for current pager state. If we want another way to render, just overwrite this method.
     * @return int[]
     */
    protected function getDisplayPages(): array
    {
        $actualPage = $this->getPositions()->getPager()->getActualPage(); // 2
        $count = $this->getPositions()->getPager()->getPagesCount(); // 20
        $whole = $this->displayPagesCount; // 10

        $half = floor($whole / 2); // 5
        $tail = $count - $actualPage; // 18

        $i = ($tail > $half) ? intval($actualPage - $half) : intval($count - $whole + 1); // 3
        $result = [];

        // ++ < 10 && 3 <= 20
        while ((count($result) < $this->displayPagesCount) && ($i <= $count)) {
            if ($this->getPositions()->getPager()->pageExists($i)) {
                $result[] = $i;
            }
            $i++;
        }

        return $result;
    }
}
