<?php

namespace kalanis\kw_sorter;


/**
 * Class SortEntry
 * @package kalanis\kw_sorter
 * Basic implementation of single entry for sorting
 */
class SortByEntry implements Interfaces\ISortEntry
{
    protected $key = '';
    protected $direction = self::DIRECTION_ASC;

    public function setKey(string $key): Interfaces\ISortEntry
    {
        $this->key = $key;
        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setDirection(string $direction): Interfaces\ISortEntry
    {
        $this->direction = in_array($direction, [static::DIRECTION_ASC, static::DIRECTION_DESC]) ? $direction : $this->direction;
        return $this;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}
