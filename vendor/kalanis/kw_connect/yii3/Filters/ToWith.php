<?php

namespace kalanis\kw_connect\yii3\Filters;


/**
 * Class ToWith
 * @package kalanis\kw_connect\yii3\Filters
 */
class ToWith extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->andWhere(['<=', $colName, $value]);
        }
        return $this;
    }
}
