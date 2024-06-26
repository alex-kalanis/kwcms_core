<?php

namespace kalanis\kw_connect\dibi\Filters;


use Dibi\Fluent;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_connect\core\Interfaces\IFilterType;


/**
 * Class AType
 * @package kalanis\kw_connect\dibi\Filters
 */
abstract class AType implements IFilterType
{
    protected ?Fluent $dibiFluent = null;

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

    /**
     * @throws ConnectException
     * @return Fluent
     */
    public function getSource(): Fluent
    {
        if (!$this->dibiFluent) {
            throw new ConnectException('Set the datasource first!');
        }
        return $this->dibiFluent;
    }
}
