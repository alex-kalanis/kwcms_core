<?php

namespace kalanis\kw_connect_inputs\Entries;


use kalanis\kw_filter\Interfaces\IFilterEntry;


/**
 * Class FilterEntry
 * @package kalanis\kw_connect_inputs\Entries
 * Simple entry of filter config - just what entry is important for filtering
 */
class FilterEntry extends AEntry
{
    protected static $defaultLimits = [
        IFilterEntry::RELATION_EQUAL,
        IFilterEntry::RELATION_NOT_EQUAL,
        IFilterEntry::RELATION_LESS,
        IFilterEntry::RELATION_LESS_EQ,
        IFilterEntry::RELATION_MORE,
        IFilterEntry::RELATION_MORE_EQ,
        IFilterEntry::RELATION_EMPTY,
        IFilterEntry::RELATION_NOT_EMPTY,
        IFilterEntry::RELATION_IN,
        IFilterEntry::RELATION_NOT_IN,
    ];

    public function setEntry(string $key, string $limitationKey, string $defaultLimit): self
    {
        $this->key = $key;
        $this->limitationKey = $limitationKey;
        $this->defaultLimit = $this->defaultLimits($defaultLimit);
        return $this;
    }

    protected function defaultLimits(string $source): string
    {
        return in_array($source, static::$defaultLimits) ? $source : $this->defaultLimit;
    }
}
