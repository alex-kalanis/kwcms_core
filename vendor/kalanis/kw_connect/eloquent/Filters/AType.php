<?php

namespace kalanis\kw_connect\eloquent\Filters;


use Illuminate\Database\Eloquent\Builder;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_connect\core\Interfaces\IFilterType;


/**
 * Class AType
 * @package kalanis\kw_connect\eloquent\Filters
 */
abstract class AType implements IFilterType
{
    protected ?Builder $eloquentBuilder = null;

    public function setDataSource($dataSource)
    {
        if (!$dataSource instanceof Builder) {
            throw new ConnectException('Param $dataSource must be an instance of \Illuminate\Database\Eloquent\Builder.');
        }

        $this->eloquentBuilder = $dataSource;
        return $this;
    }

    public function getDataSource()
    {
        return $this->eloquentBuilder;
    }

    /**
     * @throws ConnectException
     * @return Builder
     */
    public function getSource(): Builder
    {
        if (!$this->eloquentBuilder) {
            throw new ConnectException('Set the datasource first!');
        }
        return $this->eloquentBuilder;
    }
}
