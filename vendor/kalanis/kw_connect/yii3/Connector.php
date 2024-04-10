<?php

namespace kalanis\kw_connect\yii3;


use kalanis\kw_connect\core\AConnector;
use kalanis\kw_connect\core\Interfaces\IFilterFactory;
use kalanis\kw_connect\core\Interfaces\IFilterSubs;
use kalanis\kw_connect\core\Interfaces\IIterableConnector;
use kalanis\kw_connect\core\Interfaces\IOrder;
use kalanis\kw_connect\core\Interfaces\IRow;
use kalanis\kw_connect\core\Rows\SimpleArrayRow;
use Yiisoft\Db\Query\Query;


/**
 * Class Connector
 * @package kalanis\kw_connect\yii3
 * Data source is Dibi\Fluent
 */
class Connector extends AConnector implements IIterableConnector
{
    protected Query $yiiFluent;
    protected string $primaryKey;
    /** @var array<int, array<string>> */
    protected array $sorters = [];
    protected ?int $limit = null;
    protected ?int $offset = null;
    /** @var array[][] */
    protected array $rawData = [];
    protected bool $dataFetched = false;

    public function __construct(Query $dataSource, string $primaryKey)
    {
        $this->yiiFluent = $dataSource;
        $this->primaryKey = $primaryKey;
    }

    public function setFiltering(string $colName, string $filterType, $value): void
    {
        $type = $this->getFilterFactory()->getFilter($filterType);
        if ($type instanceof IFilterSubs) {
            $type->addFilterFactory($this->getFilterFactory());
        }
        $type->setDataSource($this->yiiFluent);
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
        $dataSource = clone $this->yiiFluent;
        return intval($dataSource->count($this->primaryKey));
    }

    public function fetchData(): void
    {
        foreach (array_reverse($this->sorters) as list($colName, $direction)) {
            $dir = IOrder::ORDER_ASC == $direction ? SORT_ASC : SORT_DESC ;
            $this->yiiFluent->addOrderBy([$colName, $dir]);
        }
        if (!is_null($this->offset)) {
            $this->yiiFluent->offset($this->offset);
        }
        if (!is_null($this->limit)) {
            $this->yiiFluent->limit($this->limit);
        }
        $this->rawData = $this->yiiFluent->select('*')->all();
        $this->parseData();
    }

    protected function parseData(): void
    {
        foreach ($this->rawData as $mapper) {
            $this->translatedData[$this->getPrimaryKey($mapper)] = $this->getTranslated($mapper);
        }
    }

    protected function getTranslated(array $data): IRow
    {
        return new SimpleArrayRow($data);
    }

    protected function getPrimaryKey(array $record): string
    {
        return strval($record[$this->primaryKey]);
    }

    public function getFilterFactory(): IFilterFactory
    {
        return Filters\Factory::getInstance();
    }
}
