<?php

namespace kalanis\kw_connect\core;


/**
 * Class AIterator
 * @package kalanis\kw_connect\core
 * Iterate over specific inner variable
 */
abstract class AIterator implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Iterable variable
     * @var array
     */
    protected $iterable = [];

    /**
     * Name of iterable variable;
     * @return string
     */
    abstract protected function getIterableName(): string;

    public function getIterator()
    {
        return new \ArrayIterator($this->{$this->getIterableName()});
    }

    public function offsetExists($offset)
    {
        return isset($this->{$this->getIterableName()}[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->{$this->getIterableName()}[$offset] : null ;
    }

    public function offsetSet($offset, $value)
    {
        $this->{$this->getIterableName()}[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->{$this->getIterableName()}[$offset]);
        }
    }

    public function count()
    {
        return count($this->{$this->getIterableName()});
    }
}
