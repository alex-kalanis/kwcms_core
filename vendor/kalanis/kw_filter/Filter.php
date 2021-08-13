<?php

namespace kalanis\kw_filter;


use Traversable;


/**
 * Class Filter
 * @package kalanis\kw_filter
 * Filter for selecting wanted items - structure
 */
class Filter extends AFilterEntry implements Interfaces\IFilter
{
    protected static $relations = [
        self::RELATION_EVERYTHING,
        self::RELATION_ANYTHING,
    ];

    /** @var Interfaces\IFilter[] */
    protected $entries = [];
    /** @var string */
    protected $relation = self::RELATION_EVERYTHING;

    public function getEntries(): Traversable
    {
        yield from $this->entries;
    }

    public function setValue($value): Interfaces\IFilterEntry
    {
        if ($value instanceof Interfaces\IFilterEntry) {
            $this->addFilter($value);
        }
        return $this;
    }

    public function addFilter(Interfaces\IFilterEntry $filter): Interfaces\IFilter
    {
        $this->entries[] = $filter;
        return $this;
    }

    public function remove(string $inputKey): Interfaces\IFilter
    {
        foreach ($this->entries as $index => $entry) {
            if ($entry->getKey() == $inputKey) {
                unset($this->entries[$index]);
            }
        }
        return $this;
    }

    public function clear(): Interfaces\IFilter
    {
        $this->entries = [];
        return $this;
    }

    public function getDefaultItem(): Interfaces\IFilterEntry
    {
        return new FilterEntry();
    }
}
