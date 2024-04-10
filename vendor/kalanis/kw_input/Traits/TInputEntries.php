<?php

namespace kalanis\kw_input\Traits;


use ArrayIterator;
use kalanis\kw_input\Entries\Entry;
use kalanis\kw_input\Interfaces;
use Traversable;


/**
 * Trait TInputEntries
 * @package kalanis\kw_input\Traits
 */
trait TInputEntries
{
    use TNullBytes;

    /** @var array<string, Interfaces\IEntry> */
    protected array $input = [];

    /**
     * @param string|int $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->input[$this->removeNullBytes(strval($offset))]);
    }

    /**
     * @param string|int $offset
     * @return Interfaces\IEntry|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->input[$this->removeNullBytes(strval($offset))] : null;
    }

    /**
     * @param string|int $offset
     * @param mixed|null $value
     */
    public function offsetSet($offset, $value): void
    {
        $offset = $this->removeNullBytes(strval($offset));
        if ($this->offsetExists($offset)) {
            $current = $this->offsetGet($offset);
            $source = $current && !empty($current->getSource())
                ? $current->getSource()
                : $this->defaultSource()
            ;
            $entry = new Entry();
            if (is_object($value) && ($value instanceof Interfaces\IEntry)) {
                $entry->setEntry(strval($source), $offset, $value->getValue());
            } else {
                $entry->setEntry(strval($source), $offset, $value);
            }
            $this->input[$offset] = $entry;
        } elseif ($value instanceof Interfaces\IEntry) {
            $entry = new Entry();
            $entry->setEntry($this->defaultSource(), $offset, $value->getValue());
            $this->input[$offset] = $value;
        } else {
            $entry = new Entry();
            $entry->setEntry($this->defaultSource(), $offset, $value);
            $this->input[$offset] = $entry;
        }
    }

    /**
     * @param string|int $offset
     */
    public final function offsetUnset($offset): void
    {
        if ($this->offsetExists($offset)) {
            unset($this->input[$this->removeNullBytes(strval($offset))]);
        }
    }

    /**
     * Return all inputs as array iterator
     * @return Traversable<string, Interfaces\IEntry>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator(
            $this->input,
            ArrayIterator::STD_PROP_LIST | ArrayIterator::ARRAY_AS_PROPS
        );
    }

    abstract protected function defaultSource(): string;
}
