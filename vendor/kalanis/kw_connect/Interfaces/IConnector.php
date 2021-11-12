<?php

namespace kalanis\kw_connect\Interfaces;


use kalanis\kw_connect\ConnectException;


/**
 * Interface IConnector
 * @package kalanis\kw_connect\Interfaces
 * Connect datasource to table representation and work with it
 */
interface IConnector
{
    /**
     * @param string $colName
     * @param string $value
     * @param IFilterType $type
     * @throws ConnectException
     */
    public function setFiltering(string $colName, string $value, IFilterType $type): void;

    /**
     * @param string $colName
     * @param string $direction
     * @throws ConnectException
     */
    public function setSorting(string $colName, string $direction): void;

    /**
     * @param int|null $offset
     * @param int|null $limit
     * @throws ConnectException
     */
    public function setPagination(?int $offset, ?int $limit): void;

    /**
     * @return int
     * @throws ConnectException
     */
    public function getTotalCount(): int;

    /**
     * @throws ConnectException
     */
    public function fetchData(): void;

    /**
     * Get factory of types for current filter
     * @return IFilterFactory
     */
    public function getFilterFactory(): IFilterFactory;

    /**
     * Get cell content by preset key
     * @param string|int $key
     * @return mixed
     */
    public function getByKey($key);
}
