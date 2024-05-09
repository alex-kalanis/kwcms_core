<?php

namespace kalanis\kw_connect\yii3\Filters;


/**
 * Class From
 * @package kalanis\kw_connect\yii3\Filters
 */
class From extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->andWhere(['>', $colName, $value]);
        }
        return $this;
    }
}
