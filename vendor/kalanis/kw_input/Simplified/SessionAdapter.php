<?php

namespace kalanis\kw_input\Simplified;


use ArrayIterator;
use kalanis\kw_input\Interfaces;
use kalanis\kw_input\Traits;
use Traversable;


/**
 * Class SessionAdapter
 * @package kalanis\kw_input\Extras
 * Accessing _SESSION via ArrayAccess
 */
class SessionAdapter implements Interfaces\IFilteredInputs
{
    use Traits\TFill;

    /**
     * @param string|int $offset
     * @return mixed
     */
    public final function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    /**
     * @param string|int $offset
     * @param mixed|null $value
     */
    public final function __set($offset, $value): void
    {
        $this->offsetSet($offset, $value);
    }

    /**
     * @param string|int $offset
     * @return bool
     */
    public final function __isset($offset): bool
    {
        return $this->offsetExists($offset);
    }

    /**
     * @param string|int $offset
     */
    public final function __unset($offset): void
    {
        $this->offsetUnset($offset);
    }

    public final function offsetExists($offset): bool
    {
        return isset($_SESSION[$this->removeNullBytes(strval($offset))]);
    }

    #[\ReturnTypeWillChange]
    public final function offsetGet($offset)
    {
        return $_SESSION[$this->removeNullBytes(strval($offset))];
    }

    public final function offsetSet($offset, $value): void
    {
        $_SESSION[$this->removeNullBytes(strval($offset))] = $value;
    }

    public final function offsetUnset($offset): void
    {
        unset($_SESSION[$this->removeNullBytes(strval($offset))]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator(
            $this->fillFromEntries(Interfaces\IEntry::SOURCE_SESSION, $_SESSION),
            ArrayIterator::STD_PROP_LIST | ArrayIterator::ARRAY_AS_PROPS
        );
    }
}
