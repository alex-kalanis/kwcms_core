<?php

namespace kalanis\kw_templates;


use ArrayAccess, IteratorAggregate, Traversable, Countable, ArrayIterator;


/**
 * Abstraction of HTML element - this is compact class which only needs extending
 * @author Adam Dornak original
 * @author Petr Plsek refactored
 */
abstract class AHtmlElement implements Interfaces\IHtmlElement, ArrayAccess, IteratorAggregate, Countable
{
    use HtmlElement\THtmlElement;

    /**
     * Alias for render() - for using by re-typing
     * @return string
     */
    public final function __toString()
    {
        return $this->render();
    }

    /**
     * Implementing ArrayAccess
     * @param string|int|null $offset
     * @param mixed $value
     */
    public final function offsetSet($offset, $value): void
    {
        $this->addChild($value, $offset);
    }

    /**
     * Implementing ArrayAccess
     * @param string|int $offset
     * @return bool
     */
    public final function offsetExists($offset): bool
    {
        return $this->__isset($offset);
    }

    /**
     * Implementing ArrayAccess
     * @param string|int $offset
     */
    public final function offsetUnset($offset): void
    {
        $this->removeChild($offset);
    }

    /**
     * Implementing ArrayAccess
     * @param string|int $offset
     * @return Interfaces\IHtmlElement|null
     */
    public final function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * Implementing IteratorAggregate
     * Return all children as array iterator
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->children);
    }

    /**
     * Implementing Countable
     * @return int
     */
    public final function count(): int
    {
        return count($this->children);
    }
}
