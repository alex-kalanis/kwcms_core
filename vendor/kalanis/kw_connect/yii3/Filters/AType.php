<?php

namespace kalanis\kw_connect\yii3\Filters;


use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_connect\core\Interfaces\IFilterType;
use Yiisoft\Db\Query\Query;


/**
 * Class AType
 * @package kalanis\kw_connect\yii3\Filters
 */
abstract class AType implements IFilterType
{
    protected ?Query $yiiFluent = null;

    public function setDataSource($dataSource)
    {
        if (!$dataSource instanceof Query) {
            throw new ConnectException('Param $dataSource must be an instance of \Dibi\Fluent.');
        }

        $this->yiiFluent = $dataSource;
        return $this;
    }

    public function getDataSource()
    {
        return $this->yiiFluent;
    }

    /**
     * @throws ConnectException
     * @return Query
     */
    public function getSource(): Query
    {
        if (!$this->yiiFluent) {
            throw new ConnectException('Set the datasource first!');
        }
        return $this->yiiFluent;
    }
}
