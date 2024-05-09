<?php

namespace kalanis\kw_paging\Render;


/**
 * Class CliExpandPager
 * @package kalanis\kw_paging\Render
 * Pager for displaying on CLI
 */
class CliExpandPager extends CliPager
{
    public function render(bool $showPositions = true): string
    {
        if (!$this->getPositions()->prevPageExists() && !$this->getPositions()->nextPageExists()) {
            return $this->getFilledText($this->getPositions());
        }
        $pages = [];

        $pages[] = $this->getPositions()->prevPageExists() ? static::PREV_PAGE . static::PREV_PAGE . ' ' . $this->getPositions()->getFirstPage() : static::NONE_PAGE . static::NONE_PAGE ;
        $pages[] = $this->getPositions()->prevPageExists() ? static::PREV_PAGE . ' ' . $this->getPositions()->getPrevPage() : static::NONE_PAGE ;

        foreach ($this->getDisplayPages() as $displayPage) {
            $current = ($this->getPositions()->getPager()->getActualPage() == $displayPage);
            $pages[] = $current ? static::SELECT_PAGE . $displayPage . static::SELECT_PAGE : $displayPage ;
        }

        $pages[] = $this->getPositions()->nextPageExists() ? $this->getPositions()->getNextPage() . ' ' . static::NEXT_PAGE : static::NONE_PAGE ;
        $pages[] = $this->getPositions()->nextPageExists() ? $this->getPositions()->getLastPage() . ' ' . static::NEXT_PAGE . static::NEXT_PAGE : static::NONE_PAGE . static::NONE_PAGE ;

        return implode(' | ', $pages) . ( $showPositions ? (PHP_EOL . $this->getFilledText($this->getPositions()) ) : '');
    }
}
