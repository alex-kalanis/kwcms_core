<?php

namespace kalanis\kw_connect\core\Filters\Arrays;


use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_connect\core\Filters\Arrays;
use kalanis\kw_connect\core\Interfaces\IFilterType;


/**
 * Class AType
 * @package kalanis\kw_connect\Filters\Arrays
 */
abstract class AType implements IFilterType
{
    /** @var Arrays */
    protected $dataSource;

    /**
     * @param Arrays $dataSource
     * @return $this
     * @throws ConnectException
     */
    public function setDataSource($dataSource)
    {
        if (!$dataSource instanceof Arrays) {
            throw new ConnectException('Param $dataSource must be an instance of \kalanis\kw_table\core\Connector\Filter\Arrays.');
        }

        $this->dataSource = $dataSource;
        return $this;
    }

    public function getDataSource()
    {
        return $this->dataSource;
    }
}
