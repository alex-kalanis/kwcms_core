<?php

namespace kalanis\kw_connect\search;


use kalanis\kw_connect\core\Connectors\AConnector;
use kalanis\kw_connect\core\Interfaces\IConnector;
use kalanis\kw_connect\core\Interfaces\IFilterFactory;
use kalanis\kw_connect\core\Interfaces\IFilterType;
use kalanis\kw_connect\core\Interfaces\IRow;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_mapper\Search\Search as MapperSearch;


/**
 * Class Connector
 * @package kalanis\kw_table\Connector\Sources
 * Data source is kw_mapper/Search
 */
class Connector extends AConnector implements IConnector
{
    /** @var MapperSearch */
    public $dataSource;

    /** @var ARecord[] */
    protected $rawData = [];

    /** @var bool */
    protected $dataFetched = false;

    public function __construct(MapperSearch $search)
    {
        $this->dataSource = $search;
    }

    protected function parseData(): void
    {
        foreach ($this->rawData as $mapper) {
            $this->translatedData[$this->getPrimaryKey($mapper)] = $this->getTranslated($mapper);
        }
    }

    protected function getTranslated(ARecord $data): IRow
    {
        return new Row($data);
    }

    /**
     * @param ARecord $record
     * @return string
     * @throws MapperException
     */
    protected function getPrimaryKey(ARecord $record): string
    {
        $pks = $record->getMapper()->getPrimaryKeys();
        $values = [];
        foreach ($pks as $pk) {
            $values[] = strval($record->offsetGet($pk));
        }
        return implode('_', $values);
    }

    public function setFiltering(string $colName, $value, IFilterType $type): void
    {
        $type->setDataSource($this->dataSource);
        $type->setFiltering($colName, $value);
    }

    public function setSorting(string $colName, string $direction): void
    {
        $this->dataSource->orderBy($colName, $direction);
    }

    public function setPagination(?int $offset, ?int $limit): void
    {
        $this->dataSource->offset($offset);
        $this->dataSource->limit($limit);
    }

    public function getTotalCount(): int
    {
        return $this->dataSource->getCount();
    }

    public function fetchData(): void
    {
        $this->rawData = $this->dataSource->getResults();
        $this->parseData();
    }

    public function getFilterFactory(): IFilterFactory
    {
        return Filters\Factory::getInstance();
    }
}