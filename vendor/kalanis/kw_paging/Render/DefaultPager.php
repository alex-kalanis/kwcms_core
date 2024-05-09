<?php

namespace kalanis\kw_paging\Render;


use kalanis\kw_pager\Interfaces\IPager;
use kalanis\kw_paging\Interfaces;
use kalanis\kw_paging\Traits;
use kalanis\kw_paging\Translations;


/**
 * Class DefaultPager
 * @package kalanis\kw_paging\Render
 * Port of pager from running project. Not so nice, only basics here
 * Main problem is too many templates and some of them are not used
 */
class DefaultPager implements Interfaces\IOutput
{
    use Traits\TDisplayPages;

    protected Interfaces\ILink $link;
    protected DefaultPager\Pager $pager;
    protected DefaultPager\PrevPage $prevPage;
    protected DefaultPager\PrevPageDisabled $prevPageDis;
    protected DefaultPager\CurrentPage $currentPage;
    protected DefaultPager\AnotherPage $anotherPage;
    protected DefaultPager\NextPage $nextPage;
    protected DefaultPager\NextPageDisabled $nextPageDis;

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
        $this->pager = new DefaultPager\Pager();
        $this->pager->setKpgLang($lang ?: new Translations());
        $this->prevPage = new DefaultPager\PrevPage();
        $this->prevPageDis = new DefaultPager\PrevPageDisabled();
        $this->currentPage = new DefaultPager\CurrentPage();
        $this->anotherPage = new DefaultPager\AnotherPage();
        $this->nextPage = new DefaultPager\NextPage();
        $this->nextPageDis = new DefaultPager\NextPageDisabled();
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
        foreach ($this->getDisplayPages() as $displayPage) {
            if ($this->getPositions()->getPager()->getActualPage() == $displayPage) {
                $pages[] = $this->currentPage->reset()->setData($this->link, $displayPage)->render();
            } else {
                $pages[] = $this->anotherPage->reset()->setData($this->link, $displayPage)->render();
            }
        }

        $this->pager->setData(
            $this->getPositions()->prevPageExists() ? $this->prevPage->setData($this->link, $this->getPositions())->render() : $this->prevPageDis->render(),
            $this->getPositions()->nextPageExists() ? $this->nextPage->setData($this->link, $this->getPositions())->render() : $this->nextPageDis->render(),
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
