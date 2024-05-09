<?php

namespace kalanis\kw_input\Filtered;


use Countable;
use kalanis\kw_input\Interfaces;
use kalanis\kw_input\Traits\TInputEntries;


/**
 * Class FilterAdapter
 * @package kalanis\kw_input\Extras
 * Accessing filtered inputs via ArrayAccess
 */
class FilterAdapter implements Interfaces\IFilteredInputs, Countable
{
    use TInputEntries;

    /**
     * @param Interfaces\IFiltered $filtered
     * @param string[] $entrySources
     */
    public function __construct(Interfaces\IFiltered $filtered, array $entrySources = [])
    {
        $this->input = $filtered->getInArray(null, $entrySources);
    }

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

    public final function count(): int
    {
        return count($this->input);
    }

    protected function defaultSource(): string
    {
        return Interfaces\IEntry::SOURCE_EXTERNAL;
    }
}
