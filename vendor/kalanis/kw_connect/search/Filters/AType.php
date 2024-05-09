<?php

namespace kalanis\kw_connect\search\Filters;


use kalanis\kw_connect\core\Interfaces\IFilterType;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_mapper\Search\Search;


/**
 * Class AType
 * @package kalanis\kw_connect\search\Filters
 */
abstract class AType implements IFilterType
{
    /** @var Search */
    protected ?Search $search = null;

    /**
     * @param Search $dataSource
     * @throws ConnectException
     * @return $this
     */
    public function setDataSource($dataSource)
    {
        if (!$dataSource instanceof Search) {
            throw new ConnectException('Param $dataSource must be an instance of \kalanis\kw_mapper\Search\Search.');
        }

        $this->search = $dataSource;
        return $this;
    }

    public function getDataSource()
    {
        return $this->search;
    }

    /**
     * @throws ConnectException
     * @return Search
     */
    public function getSource(): Search
    {
        if (!$this->search) {
            throw new ConnectException('Set the datasource first!');
        }
        return $this->search;
    }
}
