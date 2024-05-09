<?php

namespace kalanis\kw_paging\Render;


use kalanis\kw_pager\Interfaces\IPager;
use kalanis\kw_paging\Interfaces;
use kalanis\kw_paging\Traits;
use kalanis\kw_paging\Translations;


/**
 * Class SimplifiedPager
 * @package kalanis\kw_paging\Render
 * Simplified pager with less classes
 */
class SimplifiedPager implements Interfaces\IOutput
{
    use Traits\TDisplayPages;

    public const PREV_PAGE = '&lt;';
    public const NEXT_PAGE = '&gt;';

    protected Interfaces\ILink $link;
    protected SimplifiedPager\Pager $pager;
    protected SimplifiedPager\CurrentPage $currentPage;
    protected SimplifiedPager\AnotherPage $anotherPage;
    protected SimplifiedPager\DisabledPage $disabledPage;

    public function __construct(
        Interfaces\IPositions $positions,
        Interfaces\ILink $link,
        int $displayPages = Interfaces\IPositions::DEFAULT_DISPLAY_PAGES_COUNT,
        ?Interfaces\IPGTranslations $lang = null
    )
    {
        $this->positions = $positions;
        $this->link = $link;
        $this->displayPagesCount = $displayPages;
        $this->pager = new SimplifiedPager\Pager();
        $this->pager->setKpgLang($lang ?: new Translations());
        $this->currentPage = new SimplifiedPager\CurrentPage();
        $this->anotherPage = new SimplifiedPager\AnotherPage();
        $this->disabledPage = new SimplifiedPager\DisabledPage();
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

        $first = $this->getPositions()->prevPageExists() ? $this->anotherPage : $this->disabledPage;
        $this->link->setPageNumber($this->getPositions()->getFirstPage());
        $pages[] = $first->reset()->setData($this->link, static::PREV_PAGE . static::PREV_PAGE)->render();
        $this->link->setPageNumber($this->getPositions()->getPrevPage());
        $pages[] = $first->reset()->setData($this->link, static::PREV_PAGE)->render();

        foreach ($this->getDisplayPages() as $displayPage) {
            $current = ($this->getPositions()->getPager()->getActualPage() == $displayPage) ? $this->currentPage : $this->anotherPage ;
            $this->link->setPageNumber($displayPage);
            $pages[] = $current->reset()->setData($this->link, strval($displayPage))->render();
        }

        $last = $this->getPositions()->nextPageExists() ? $this->anotherPage : $this->disabledPage;
        $this->link->setPageNumber($this->getPositions()->getNextPage());
        $pages[] = $last->reset()->setData($this->link, static::NEXT_PAGE)->render();
        $this->link->setPageNumber($this->getPositions()->getLastPage());
        $pages[] = $last->reset()->setData($this->link, static::NEXT_PAGE . static::NEXT_PAGE)->render();

        $this->pager->setData(
            implode('', $pages),
            $showPositions ? $this->getPositions() : null
        );
        return $this->pager->render();
    }

    public function getPager(): IPager
    {
        return $this->getPositions()->getPager();
    }
}
