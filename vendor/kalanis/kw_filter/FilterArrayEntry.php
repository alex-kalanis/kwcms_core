<?php

namespace kalanis\kw_filter;


/**
 * Class FilterArrayEntry
 * @package kalanis\kw_filter
 * Filtering by array, not by just simple compare
 */
class FilterArrayEntry extends AFilterEntry
{
    protected static $relations = [
        self::RELATION_IN,
        self::RELATION_NOT_IN,
    ];

    protected $relation = self::RELATION_IN;
    protected $value = [];

    public function setValue($value): Interfaces\IFilterEntry
    {
        $this->value = (array)$value;
        return $this;
    }
}
