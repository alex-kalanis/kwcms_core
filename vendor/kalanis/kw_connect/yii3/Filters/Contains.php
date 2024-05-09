<?php

namespace kalanis\kw_connect\yii3\Filters;


/**
 * Class Contains
 * @package kalanis\kw_connect\yii3\Filters
 */
class Contains extends AType
{
    public function setFiltering(string $colName, $value)
    {
        if ('' !== $value) {
            $this->getSource()->andWhere(['like', $colName, strval($value)]);
        }
        return $this;
    }
}
