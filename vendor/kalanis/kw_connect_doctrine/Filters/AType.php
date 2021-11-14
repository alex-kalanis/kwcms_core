<?php

namespace kalanis\kw_connect_doctrine\Filters;


use Doctrine\DBAL\Query\QueryBuilder;
use kalanis\kw_connect\ConnectException;
use kalanis\kw_connect\Interfaces\IFilterType;


/**
 * Class AType
 * @package kalanis\kw_connect_doctrine\Filters
 */
abstract class AType implements IFilterType
{
    /** @var QueryBuilder */
    protected $queryBuilder;

    /**
     * @param QueryBuilder $dataSource
     * @return $this
     * @throws ConnectException
     */
    public function setDataSource($dataSource)
    {
        if (!$dataSource instanceof QueryBuilder) {
            throw new ConnectException('Param $dataSource must be an instance of \Doctrine\DBAL\Query\QueryBuilder.');
        }

        $this->queryBuilder = $dataSource;
        return $this;
    }

    public function getDataSource()
    {
        return $this->queryBuilder;
    }
}
