<?php

namespace kalanis\kw_address_handler;


use ArrayAccess;


/**
 * Class Params
 * @package kalanis\kw_address_handler\Sources
 * Class for accessing params inside the address as array
 * Not ArrayIterator due memory consumption
 */
class Params implements ArrayAccess
{
    /** @var string[] */
    protected $paramsData = [];

    public function setParamsData(iterable $data): self
    {
        $this->paramsData = $data;
        return $this;
    }

    public function getParamsData(): iterable
    {
        return $this->paramsData;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->paramsData[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->paramsData[$offset] : null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->paramsData[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->paramsData[$offset]);
    }
}
