<?php

namespace kalanis\kw_connect\eloquent;


use ArrayAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use kalanis\kw_connect\core\AConnector;
use kalanis\kw_connect\core\Interfaces\IFilterFactory;
use kalanis\kw_connect\core\Interfaces\IFilterSubs;
use kalanis\kw_connect\core\Interfaces\IIterableConnector;
use kalanis\kw_connect\core\Interfaces\IOrder;
use kalanis\kw_connect\core\Interfaces\IRow;
use kalanis\kw_connect\core\Rows\ArrayAccessRow;


/**
 * Class Connector
 * @package kalanis\kw_connect\eloquent
 * Data source is Laravel\Eloquent
 */
class Connector extends AConnector implements IIterableConnector
{
    protected Builder $queryBuilder;
    protected string $primaryKey;
    /** @var array<int, array<string>> */
    protected array $sorters = [];
    protected ?int $limit = null;
    protected ?int $offset = null;
    /** @var Collection */
    protected $rawData = null;
    protected bool $dataFetched = false;

    public function __construct(Builder $queryBuilder, string $primaryKey)
    {
        $this->queryBuilder = $queryBuilder;
        $this->primaryKey = $primaryKey;
    }

    public function setFiltering(string $colName, string $filterType, $value): void
    {
        $type = $this->getFilterFactory()->getFilter($filterType);
        if ($type instanceof IFilterSubs) {
            $type->addFilterFactory($this->getFilterFactory());
        }
        $type->setDataSource($this->queryBuilder);
        $type->setFiltering($colName, $value);
    }

    public function setOrdering(string $colName, string $direction): void
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
        $dataSource = clone $this->queryBuilder;
        return $dataSource->count();
    }

    public function fetchData(): void
    {
        foreach (array_reverse($this->sorters) as list($colName, $direction)) {
            $dir = IOrder::ORDER_ASC == $direction ? 'asc' : 'desc' ;
            $this->queryBuilder->orderBy($colName, $dir);
        }
        if (!is_null($this->offset)) {
            $this->queryBuilder->offset($this->offset);
        }
        if (!is_null($this->limit)) {
            $this->queryBuilder->limit($this->limit);
        }
        $this->rawData = $this->queryBuilder->get();
        $this->parseData();
    }

    protected function parseData(): void
    {
        foreach ($this->rawData->getIterator() as $iterate) {
            $this->translatedData[$this->getPrimaryKey($iterate)] = $this->getTranslated($iterate);
        }
    }

    protected function getTranslated(ArrayAccess $data): IRow
    {
        return new ArrayAccessRow($data);
    }

    protected function getPrimaryKey(ArrayAccess $record): string
    {
        return strval($record->offsetGet($this->primaryKey));
    }

    public function getFilterFactory(): IFilterFactory
    {
        return Filters\Factory::getInstance();
    }
}
