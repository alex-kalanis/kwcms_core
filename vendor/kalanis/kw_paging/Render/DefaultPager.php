<?php

namespace kalanis\kw_paging\Render;


use kalanis\kw_pager\Interfaces\IPager;
use kalanis\kw_paging\Interfaces\ILink;
use kalanis\kw_paging\Interfaces\IOutput;
use kalanis\kw_paging\Interfaces\IPGTranslations;
use kalanis\kw_paging\Interfaces\IPositions;


/**
 * Class DefaultPager
 * @package kalanis\kw_paging\Render
 * Port of pager from running project. Not so nice, only basics here
 * Main problem is too many templates and some of them are not used
 */
class DefaultPager implements IOutput
{
    use TDisplayPages;

    protected $link = null;
    protected $pager = null;
    protected $prevPage = null;
    protected $prevPageDis = null;
    protected $currentPage = null;
    protected $anotherPage = null;
    protected $nextPage = null;
    protected $nextPageDis = null;

    public function __construct(IPositions $positions, ILink $link, int $displayPages = IPositions::DEFAULT_DISPLAY_PAGES_COUNT, ?IPGTranslations $lang = null)
    {
        $this->positions = $positions;
        $this->link = $link;
        $this->displayPagesCount = $displayPages;
        $this->pager = new DefaultPager\Pager();
        $this->pager->setLang($lang ?: new Translations());
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
        if (!$this->positions->prevPageExists() && !$this->positions->nextPageExists()) {
            return '';
        }
        $pages = [];
        foreach ($this->getDisplayPages() as $displayPage) {
            if ($this->positions->getPager()->getActualPage() == $displayPage) {
                $pages[] = $this->currentPage->reset()->setData($this->link, $displayPage)->render();
            } else {
                $pages[] = $this->anotherPage->reset()->setData($this->link, $displayPage)->render();
            }
        }

        $this->pager->setData(
            $this->positions->prevPageExists() ? $this->prevPage->setData($this->link, $this->positions)->render() : $this->prevPageDis->render(),
            $this->positions->nextPageExists() ? $this->nextPage->setData($this->link, $this->positions)->render() : $this->nextPageDis->render(),
            implode('', $pages),
            $showPositions ? $this->positions : null
        );
        return $this->pager->render();
    }

    public function getPager(): IPager
    {
        return $this->positions->getPager();
    }
}
