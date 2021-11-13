<?php

namespace kalanis\kw_connect_record;


use kalanis\kw_connect\Connectors\AConnector;
use kalanis\kw_connect\ConnectException;
use kalanis\kw_connect\Filters\Arrays;
use kalanis\kw_connect\Interfaces\IConnector;
use kalanis\kw_connect\Interfaces\IFilterFactory;
use kalanis\kw_connect\Interfaces\IFilterType;
use kalanis\kw_connect\Interfaces\IOrder;
use kalanis\kw_connect\Interfaces\IRow;


/**
 * Class Connector
 * @package kalanis\kw_connect_record
 * Datasource is kw_mapper/Record
 */
class Connector extends AConnector implements IConnector
{
    /** @var array */
    protected $dataSource;
    /** @var array */
    protected $filteredData = [];
    /** @var string */
    protected $sortDirection = IOrder::ORDER_ASC;
    /** @var string */
    protected $sortColumn = '';
    /** @var string|null */
    protected $filterByColumn = null;
    /** @var string|null */
    protected $filterByNamePart = null;
    /** @var int|null */
    protected $offset = null;
    /** @var int|null */
    protected $limit = null;

    public function __construct(array $records)
    {
        $this->dataSource = $records;
    }

    protected function parseData(): void
    {
        $filtered = array_filter(array_map([$this, 'getTranslated'], $this->dataSource), [$this, 'filterItems']);
        uasort($filtered, [$this, 'sortItems']);
        $this->filteredData = $filtered;
        $this->translatedData = array_slice($filtered, intval($this->offset), $this->limit);
    }

    public function getTranslated($data): IRow
    {
        return new Row($data);
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

    public function getTotalCount(): int
    {
        if (empty($this->dataSource)) {
            return 0;
        }
        if (empty($this->filteredData)) {
            $this->fetchData();
        }
        return count($this->filteredData);
    }

    public function fetchData(): void
    {
        $this->parseData();
    }

    /**
     * @param IRow $node
     * @return bool
     * @throws ConnectException
     */
    public function filterItems(IRow $node): bool
    {
        return is_null($this->filterByNamePart)
            || is_null($this->filterByColumn)
            || (
                $this->columnExists($node, $this->filterByColumn)
                && $this->compareValues($node, $this->filterByColumn, $this->filterByNamePart)
            );
    }

    protected function columnExists(IRow $node, string $whichColumn): bool
    {
        return $node->__isset($whichColumn);
    }

    /**
     * @param IRow $node
     * @param string $whichColumn
     * @param string $columnValue
     * @return bool
     * @throws ConnectException
     */
    protected function compareValues(IRow $node, string $whichColumn, string $columnValue): bool
    {
        return false !== stripos($node->getValue($whichColumn), $columnValue);
    }

    /**
     * @param IRow $a
     * @param IRow $b
     * @return int
     * @throws ConnectException
     */
    public function sortItems(IRow $a, IRow $b)
    {
        return
            IOrder::ORDER_ASC == $this->sortDirection
                ? $a->getValue($this->sortColumn) <=> $b->getValue($this->sortColumn)
                : $b->getValue($this->sortColumn) <=> $a->getValue($this->sortColumn)
            ;
    }

    public function getFilterFactory(): IFilterFactory
    {
        return Arrays\Factory::getInstance();
    }
}
