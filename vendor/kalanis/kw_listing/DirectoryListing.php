<?php

namespace kalanis\kw_listing;


use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_pager\BasicPager;
use kalanis\kw_paging\Positions;
use kalanis\kw_paging\Render\SimplifiedPager;


/**
 * Class DirectoryListing
 * @package KWCMS\modules\Dirlist
 * Listing through the directory
 */
class DirectoryListing
{
    /** @var IVariables */
    protected $inputs = null;
    /** @var SimplifiedPager|null */
    protected $paging = null;

    protected $path = '';
    protected $orderDesc = false;
    protected $usableCallback = null;
    protected $files = [];

    public function __construct(IVariables $inputs)
    {
        $this->inputs = $inputs;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function setOrderDesc(bool $orderDesc): self
    {
        $this->orderDesc = $orderDesc;
        return $this;
    }

    public function setUsableCallback(callable $usable): self
    {
        $this->usableCallback = $usable;
        return $this;
    }

    public function process(): self
    {
        $preList = ($this->orderDesc) ? scandir($this->path, 1) : scandir($this->path, 0) ;
        $this->files = array_filter($preList);
        $this->files = array_filter($this->files, $this->usableCallback);
        $this->paging = $this->pagerLookup();
        return $this;
    }

    protected function pagerLookup(): SimplifiedPager
    {
        $paging = new SimplifiedPager(new Positions(new BasicPager()), new Linking($this->inputs));
        $paging->getPager()->setActualPage($this->actualPageLookup())->setMaxResults(count($this->files));
        return $paging;
    }

    protected function actualPageLookup(): int
    {
        $actualPages = $this->inputs->getInArray(Linking::PAGE_KEY, [
            IEntry::SOURCE_CLI, IEntry::SOURCE_POST, IEntry::SOURCE_GET
        ]);
        return !empty($actualPages) ? intval(strval(reset($actualPages))) : Positions::FIRST_PAGE ;
    }

    public function getPaging(): ?SimplifiedPager
    {
        return $this->paging;
    }

    public function getFiles(int $limit): array
    {
        $this->paging->getPager()->setLimit($limit);
        return array_slice($this->files, $this->paging->getPager()->getOffset(), $this->paging->getPager()->getLimit());
    }

    public function getFilesChunked(int $rows, int $columns): array
    {
        return array_chunk($this->getFiles(intval($columns * $rows)), $columns);
    }
}
