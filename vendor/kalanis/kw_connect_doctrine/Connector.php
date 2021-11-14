<?php

namespace kalanis\kw_connect_doctrine;


use Doctrine\DBAL\Query\QueryBuilder;
use kalanis\kw_connect\Connectors\AConnector;
use kalanis\kw_connect\Interfaces\IConnector;
use kalanis\kw_connect\Interfaces\IFilterFactory;
use kalanis\kw_connect\Interfaces\IFilterType;
use kalanis\kw_connect\Interfaces\IRow;


/**
 * Class Connector
 * @package kalanis\kw_connect_doctrine
 * Datasource is Doctrine
 * @todo WIP - select itself and count over data
 */
class Connector extends AConnector implements IConnector
{
    /** @var QueryBuilder */
    protected $queryBuilder;
    /** @var string */
    protected $primaryKey;
    /** @var array */
    protected $sorters;
    /** @var int */
    protected $limit;
    /** @var int */
    protected $offset;
    /** @var bool */
    protected $dataFetched = false;

    public function __construct(QueryBuilder $queryBuilder, string $primaryKey)
    {
        $this->queryBuilder = $queryBuilder;
        $this->primaryKey = $primaryKey;
    }

    public function setFiltering(string $colName, string $value, IFilterType $type): void
    {
        $type->setDataSource($this->queryBuilder);
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
        return $this->queryBuilder->count('*');
    }

    public function fetchData(): void
    {
        foreach ($this->sorters as list($colName, $direction)) {
            $this->queryBuilder->orderBy(strval($colName), strval($direction));
        }
        if (!is_null($this->offset)) {
            $this->queryBuilder->setFirstResult($this->offset);
        }
        if (!is_null($this->limit)) {
            $this->queryBuilder->setMaxResults($this->limit);
        }
        $this->parseData();
    }

    protected function parseData(): void
    {
        foreach ($this->queryBuilder->fetchAllAssociative() as $mapper) {
            $this->translatedData[$this->getPrimaryKey($mapper)] = $this->getTranslated($mapper);
        }
    }

    protected function getTranslated(NetteRow $data): IRow
    {
        return new Row($data);
    }

    protected function getPrimaryKey(NetteRow $record): string
    {
        return $record->offsetGet($this->primaryKey);
    }

    public function getFilterFactory(): IFilterFactory
    {
        return Filters\Factory::getInstance();
    }
}
