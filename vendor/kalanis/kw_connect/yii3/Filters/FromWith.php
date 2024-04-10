<?php

namespace kalanis\kw_connect\yii3\Filters;


/**
 * Class FromWith
 * @package kalanis\kw_connect\yii3\Filters
 */
class FromWith extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->andWhere(['>=', $colName, $value]);
        }
        return $this;
    }
}
