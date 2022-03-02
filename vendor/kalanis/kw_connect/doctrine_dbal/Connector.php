<?php

namespace kalanis\kw_connect\doctrine_dbal;


use Doctrine\DBAL\Query\QueryBuilder;
use kalanis\kw_connect\arrays\Row;
use kalanis\kw_connect\core\AConnector;
use kalanis\kw_connect\core\Interfaces\IConnector;
use kalanis\kw_connect\core\Interfaces\IFilterFactory;
use kalanis\kw_connect\core\Interfaces\IFilterSubs;
use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class Connector
 * @package kalanis\kw_connect\doctrine_dbal
 * Data source is Doctrine DBAL
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

    public function setFiltering(string $colName, string $filterType, $value): void
    {
        $type = $this->getFilterFactory()->getFilter($filterType);
        if ($type instanceof IFilterSubs) {
            $type->addFilterFactory($this->getFilterFactory());
        }
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
        $this->queryBuilder->select('count(' . $this->primaryKey. ')');
        return intval($this->queryBuilder->fetchOne());
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
        foreach (
            $this->queryBuilder->getConnection()->iterateNumeric(
                $this->queryBuilder->getSQL(),
                $this->queryBuilder->getParameters(),
                $this->queryBuilder->getParameterTypes()
            ) as $value
        ) {
            $this->translatedData[$this->getPrimaryKey($value)] = $this->getTranslated($value);
        }
    }

    protected function getTranslated($data): IRow
    {
        return new Row($data);
    }

    protected function getPrimaryKey($data): string
    {
        return $data[$this->primaryKey];
    }

    public function getFilterFactory(): IFilterFactory
    {
        return Filters\Factory::getInstance();
    }
}
