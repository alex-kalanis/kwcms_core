<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_mapper\Interfaces\IQueryBuilder;
use kalanis\kw_table\Connector\Sources\Mapper;
use kalanis\kw_table\Interfaces\Connector\IFilterType;
use kalanis\kw_table\Interfaces\Table\IRow;


/**
 * Class SourceItem
 * @package KWCMS\modules\Images\Lib
 * Mapper is array of connecting items.
 */
class SourceItem extends Mapper
{
    protected $sortDirection = IQueryBuilder::ORDER_ASC;
    protected $sortColumn = '';
    protected $filterByColumn = null;
    protected $filterByNamePart = null;
    protected $offset = null;
    protected $limit = null;

    protected function getTranslated($data): IRow
    {
        return new ConnectItem($data);
    }

    public function setFiltering(string $colName, string $value, IFilterType $type): void
    {
        $this->filterByColumn = $colName;
        $this->filterByNamePart = $value;
    }

    public function setSorting(string $colName, string $direction): void
    {
        $this->sortColumn = $colName;
        $this->sortDirection = $direction;
    }

    public function setPagination(?int $offset, ?int $limit): void
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function fetchData(): void
    {
        $this->rawData = $this->dataSource;
        $this->parseData();
        $filtered = array_filter($this->translatedData, [$this, 'filterItems']);
        uasort($filtered, [$this, 'sortItems']);
        $this->translatedData = array_slice($filtered, $this->offset, $this->limit);
    }

    public function filterItems(ConnectItem $node): bool
    {
        return is_null($this->filterByNamePart)
            || is_null($this->filterByColumn)
            || (
                isset($node[$this->filterByColumn])
             && false !== strpos($node[$this->filterByColumn], $this->filterByNamePart)
            );
    }

    public function sortItems(ConnectItem $a, ConnectItem $b)
    {
        return
            IQueryBuilder::ORDER_ASC == $this->sortDirection
                ? $a->getValue($this->sortColumn) <=> $b->getValue($this->sortColumn)
                : $b->getValue($this->sortColumn) <=> $a->getValue($this->sortColumn)
            ;
    }
}
