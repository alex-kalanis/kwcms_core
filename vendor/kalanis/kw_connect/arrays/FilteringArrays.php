<?php

namespace kalanis\kw_connect\arrays;


use ArrayAccess;
use Countable;


/**
 * Class FilteringArrays
 * @package kalanis\kw_connect\arrays
 * Class for updating arrays via reference
 */
class FilteringArrays implements ArrayAccess, Countable
{
    protected $array;

    /**
     * @param string[]|int[]|bool[]|Row[] $array
     */
    public function __construct(array &$array)
    {
        $this->array = &$array;
    }

    /**
     * @return string[]|int[]|bool[]|Row[]
     */
    public function &getArray()
    {
        return $this->array;
    }

    /**
     * @param string[]|int[]|bool[] $array
     * @return $this
     */
    public function setArray($array)
    {
        $this->array = $array;
        return $this;
    }

    public function resetArray()
    {
        $this->array = [];
        return $this;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->array[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->array[$offset] : null ;
    }

    public function offsetSet($offset, $value): void
    {
        $this->array[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        if ($this->offsetExists($offset)) {
            unset($this->array[$offset]);
        }
    }

    public function count(): int
    {
        return count($this->array);
    }
}
