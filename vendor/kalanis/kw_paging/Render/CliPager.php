<?php

namespace kalanis\kw_paging\Render;


use kalanis\kw_pager\Interfaces\IPager;
use kalanis\kw_paging\Interfaces\IOutput;
use kalanis\kw_paging\Interfaces\IPGTranslations;
use kalanis\kw_paging\Interfaces\IPositions;
use kalanis\kw_paging\Traits;
use kalanis\kw_paging\Translations;


/**
 * Class CliPager
 * @package kalanis\kw_paging\Render
 * Pager for displaying on CLI
 */
class CliPager implements IOutput
{
    use Traits\TDisplayPages;
    use Traits\THelpingText;

    public const SELECT_PAGE = '*';
    public const NONE_PAGE = '-';
    public const PREV_PAGE = '<';
    public const NEXT_PAGE = '>';

    public function __construct(
        IPositions $positions,
        int $displayPages = IPositions::DEFAULT_DISPLAY_PAGES_COUNT,
        ?IPGTranslations $lang = null
    )
    {
        $this->positions = $positions;
        $this->displayPagesCount = $displayPages;
        $this->setKpgLang($lang ?: new Translations());
    }

    public function __toString()
    {
        return $this->render();
    }

    public function render(bool $showPositions = true): string
    {
        if (!$this->getPositions()->prevPageExists() && !$this->getPositions()->nextPageExists()) {
            return '';
        }
        $pages = [];

        $pages[] = $this->getPositions()->prevPageExists() ? static::PREV_PAGE . static::PREV_PAGE . ' ' . $this->getPositions()->getFirstPage() : static::NONE_PAGE . static::NONE_PAGE ;
        $pages[] = $this->getPositions()->prevPageExists() ? static::PREV_PAGE . ' ' . $this->getPositions()->getPrevPage() : static::NONE_PAGE ;
        $pages[] = $this->getPositions()->getPager()->getActualPage() ;
        $pages[] = $this->getPositions()->nextPageExists() ? $this->getPositions()->getNextPage() . ' ' . static::NEXT_PAGE : static::NONE_PAGE ;
        $pages[] = $this->getPositions()->nextPageExists() ? $this->getPositions()->getLastPage() . ' ' . static::NEXT_PAGE . static::NEXT_PAGE : static::NONE_PAGE . static::NONE_PAGE ;

        return implode(' | ', $pages) . ($showPositions ? ( PHP_EOL . $this->getFilledText($this->getPositions()) ) : '' );
    }

    public function getPager(): IPager
    {
        return $this->getPositions()->getPager();
    }
}
