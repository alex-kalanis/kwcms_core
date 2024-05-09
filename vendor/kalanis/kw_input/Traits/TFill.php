<?php

namespace kalanis\kw_input\Traits;


use kalanis\kw_input\Entries\Entry;
use kalanis\kw_input\Interfaces;


/**
 * Trait TFill
 * @package kalanis\kw_input\Traits
 * Fill inputs by params
 */
trait TFill
{
    use TNullBytes;

    /**
     * @param string $source
     * @param array<int|string, mixed|null> $entries
     * @return Interfaces\IEntry[]
     */
    protected function fillFromEntries(string $source, array $entries): array
    {
        $result = [];
        foreach ($entries as $key => $value) {
            $result[] = $this->fillEntryData($source, $this->removeNullBytes(strval($key)), $value);
        }
        return $result;
    }

    /**
     * @param string $source
     * @param iterable<int|string, mixed|null> $iterator
     * @return Interfaces\IEntry[]
     */
    protected function fillFromIterator(string $source, iterable $iterator): array
    {
        $result = [];
        foreach ($iterator as $key => $value) {
            $result[] = $this->fillEntryData($source, $this->removeNullBytes(strval($key)), $value);
        }
        return $result;
    }

    /**
     * @param string $source
     * @param string $key
     * @param mixed|null $value
     * @return Entry
     */
    protected function fillEntryData(string $source, string $key, $value): Entry
    {
        if (is_object($value) && ($value instanceof Interfaces\IEntry)) {
            $value = $value->getValue();
        }
        $entry = new Entry();
        return $entry->setEntry($source, $key, $value);
    }
}
