<?php

namespace kalanis\kw_forms\Adapters;


use ArrayAccess;
use Countable;
use Iterator;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_input\Interfaces\IEntry;


abstract class AAdapter implements ArrayAccess, Countable, Iterator, IEntry
{
    protected $key = null;
    protected $vars = [];

    /**
     * @param string $inputType
     * @return void
     * @throws FormsException
     */
    abstract public function loadEntries(string $inputType): void;

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->current();
    }

    public function current()
    {
        return $this->valid() ? $this->offsetGet($this->key) : null ;
    }

    public function next()
    {
        next($this->vars);
        $this->key = key($this->vars);
    }

    public function key()
    {
        return $this->key;
    }

    public function valid()
    {
        return $this->offsetExists($this->key);
    }

    public function rewind()
    {
        reset($this->vars);
        $this->key = key($this->vars);
    }

    public function offsetExists($offset)
    {
        return isset($this->vars[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->vars[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->vars[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->vars[$offset]);
    }

    public function count()
    {
        return count($this->vars);
    }

    protected function removeNullBytes($string, $nullTo = '')
    {
        return str_replace(chr(0), $nullTo, $string);
    }
}
