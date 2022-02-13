<?php

namespace kalanis\kw_connect\arrays;


use kalanis\kw_connect\core\AConnector;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_connect\core\Interfaces\IConnector;
use kalanis\kw_connect\core\Interfaces\IFilterFactory;
use kalanis\kw_connect\core\Interfaces\IFilterSubs;
use kalanis\kw_connect\core\Interfaces\IFilterType;
use kalanis\kw_connect\core\Interfaces\IOrder;
use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class Connector
 * @package kalanis\kw_connect\arrays
 * For likes there is a column finder in search mapper.
 * So it's possible to map children for sorting and filtering.
 */
class Connector extends AConnector implements IConnector
{
    /** @var string */
    protected $primaryKey = null;
    /** @var array */
    protected $dataSource = [];
    /** @var string[][] */
    protected $sorting = [];
    /** @var array */
    protected $filteredData = [];
    /** @var string */
    protected $sortDirection = IOrder::ORDER_ASC;
    /** @var IFilterType[] */
    protected $filtering = [];
    /** @var int|null */
    protected $offset = null;
    /** @var int|null */
    protected $limit = null;

    public function __construct(array $source, ?string $primaryKey = null)
    {
        $this->dataSource = $source;
        $this->primaryKey = $primaryKey;
    }

    public function setFiltering(string $colName, string $filterType, $value): void
    {
        $this->filtering[] = [$filterType, $colName, $value];
    }

    protected function getFiltered(&$data)
    {
        return new FilteringArrays($data);
    }

    public function getTranslated($data): IRow
    {
        return new Row($data);
    }

    public function setSorting(string $colName, string $direction): void
    {
        $this->sorting[] = [$colName, $direction];
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
     * @throws ConnectException
     */
    protected function parseData(): void
    {
        $translated = array_map([$this, 'getTranslated'], $this->dataSource);
        $filtered = $this->getFiltered($translated);
        foreach (array_reverse($this->filtering) as list($filterType, $columnName, $value)) {
            $type = $this->getFilterFactory()->getFilter($filterType);
            if ($type instanceof IFilterSubs) {
                $type->addFilterFactory($this->getFilterFactory());
            }
            $type->setDataSource($filtered);
            $type->setFiltering($columnName, $value);
            $filtered = $type->getDataSource();
        }

        foreach (array_reverse($this->sorting) as list($columnName, $direction)) {
            $toSort = $this->indexedArray($filtered, $columnName);
            if (IOrder::ORDER_DESC == $direction) {
                asort($toSort);
            } else {
                arsort($toSort);
            }
            $this->putItBack($filtered, $toSort);
        }

        $this->filteredData = $filtered->getArray();
        $this->translatedData = array_slice($filtered->getArray(), intval($this->offset), $this->limit);
        if (!empty($this->primaryKey)) {
            $this->translatedData = array_combine(array_map([$this, 'rowsPk'], $this->translatedData), $this->translatedData);
        }
    }

    /**
     * @param IRow $row
     * @return string
     * @throws ConnectException
     */
    public function rowsPk(IRow $row): string
    {
        return strval($row->getValue($this->primaryKey));
    }

    /**
     * @param FilteringArrays $filtered
     * @param string $columnName
     * @return array
     * @throws ConnectException
     */
    protected function indexedArray(FilteringArrays $filtered, string $columnName): array
    {
        $result = [];
        foreach ($filtered->getArray() as $index => $item) {
            $result[$index] = $item->getValue($columnName);
        }
        return $result;
    }

    protected function putItBack(FilteringArrays $filtered, array $sorted): void
    {
        $finalArray = [];
        foreach ($sorted as $key => $item) {
            $finalArray[$key] = $filtered->offsetGet($key);
        }
        $filtered->setArray($finalArray);
    }

    public function getFilterFactory(): IFilterFactory
    {
        return Filters\Factory::getInstance();
    }
}
