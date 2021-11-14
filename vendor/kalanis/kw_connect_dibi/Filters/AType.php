<?php

namespace kalanis\kw_connect_dibi\Filters;


use Dibi\Fluent;
use kalanis\kw_connect\ConnectException;
use kalanis\kw_connect\Interfaces\IFilterType;


/**
 * Class AType
 * @package kalanis\kw_connect_dibi\Filters
 */
abstract class AType implements IFilterType
{
    /** @var Fluent */
    protected $dibiFluent;

    /**
     * @param Fluent $dataSource
     * @return $this
     * @throws ConnectException
     */
    public function setDataSource($dataSource)
    {
        if (!$dataSource instanceof Fluent) {
            throw new ConnectException('Param $dataSource must be an instance of \Dibi\Fluent.');
        }

        $this->dibiFluent = $dataSource;
        return $this;
    }

    public function getDataSource()
    {
        return $this->dibiFluent;
    }
}
