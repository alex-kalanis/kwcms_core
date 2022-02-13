<?php

namespace kalanis\kw_connect\dibi;


use Dibi;
use Dibi\Fluent;
use kalanis\kw_connect\core\AConnector;
use kalanis\kw_connect\core\Interfaces\IConnector;
use kalanis\kw_connect\core\Interfaces\IFilterFactory;
use kalanis\kw_connect\core\Interfaces\IFilterSubs;
use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class Connector
 * @package kalanis\kw_connect\dibi
 * Data source is Dibi\Fluent
 */
class Connector extends AConnector implements IConnector
{
    /** @var Fluent */
    protected $dibiFluent;
    /** @var string */
    protected $primaryKey;
    /** @var array */
    protected $sorters;
    /** @var int */
    protected $limit;
    /** @var int */
    protected $offset;
    /** @var Dibi\Row[] */
    protected $rawData = [];
    /** @var bool */
    protected $dataFetched = false;

    public function __construct(Fluent $dataSource, string $primaryKey)
    {
        $this->dibiFluent = $dataSource;
        $this->primaryKey = $primaryKey;
    }

    public function setFiltering(string $colName, string $filterType, $value): void
    {
        $type = $this->getFilterFactory()->getFilter($filterType);
        if ($type instanceof IFilterSubs) {
            $type->addFilterFactory($this->getFilterFactory());
        }
        $type->setDataSource($this->dibiFluent);
        $type->setFiltering($colName, $value);
    }

    public function setSorting(string $colName, string $direction): void
    {
        $this->sorters[] = [$colName, $direction];
    }

    public function setPagination(?int $offset, ?int $limit): void
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function getTotalCount(): int
    {
        // count needs only filtered content
        $dataSource = clone $this->dibiFluent;
        return $dataSource->count();
    }

    public function fetchData(): void
    {
        foreach (array_reverse($this->sorters) as list($colName, $direction)) {
            $this->dibiFluent->orderBy($colName, $direction);
        }
        $this->rawData = $this->dibiFluent->fetchAll($this->offset, $this->limit);
        $this->parseData();
    }

    protected function parseData(): void
    {
        foreach ($this->rawData as $mapper) {
            $this->translatedData[$this->getPrimaryKey($mapper)] = $this->getTranslated($mapper);
        }
    }

    protected function getTranslated(Dibi\Row $data): IRow
    {
        return new Row($data);
    }

    protected function getPrimaryKey(Dibi\Row $record): string
    {
        return $record->offsetGet($this->primaryKey);
    }

    public function getFilterFactory(): IFilterFactory
    {
        return Filters\Factory::getInstance();
    }
}
