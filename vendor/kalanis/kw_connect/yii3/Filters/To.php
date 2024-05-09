<?php

namespace kalanis\kw_connect\yii3\Filters;


/**
 * Class To
 * @package kalanis\kw_connect\yii3\Filters
 */
class To extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->andWhere(['<', $colName, $value]);
        }
        return $this;
    }
}
