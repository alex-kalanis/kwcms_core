<?php

namespace kalanis\kw_input\Simplified;


use ArrayAccess;


/**
 * Class SessionAdapter
 * @package kalanis\kw_input\Extras
 * Accessing _SESSION via ArrayAccess
 */
class SessionAdapter implements ArrayAccess
{
    use TNullBytes;

    public final function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    public final function __set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    public final function __isset($offset)
    {
        return $this->offsetExists($offset);
    }

    public final function __unset($offset)
    {
        $this->offsetUnset($offset);
    }

    #[\ReturnTypeWillChange]
    public final function offsetExists($offset)
    {
        return isset($_SESSION[$this->removeNullBytes($offset)]);
    }

    #[\ReturnTypeWillChange]
    public final function offsetGet($offset)
    {
        return $_SESSION[$this->removeNullBytes($offset)];
    }

    #[\ReturnTypeWillChange]
    public final function offsetSet($offset, $value)
    {
        $_SESSION[$this->removeNullBytes($offset)] = $value;
    }

    #[\ReturnTypeWillChange]
    public final function offsetUnset($offset)
    {
        unset($_SESSION[$this->removeNullBytes($offset)]);
    }
}
