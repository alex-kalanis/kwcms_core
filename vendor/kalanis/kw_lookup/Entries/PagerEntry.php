<?php

namespace kalanis\kw_lookup\Entries;


use kalanis\kw_lookup\Interfaces;
use kalanis\kw_input\Interfaces\IEntry as Input;
use Traversable;


/**
 * Class PagerEntry
 * @package kalanis\kw_lookup\Entries
 * Simple entry of pager config - just what entry is important for pager
 */
class PagerEntry extends AEntry implements Interfaces\IPagerEntry
{
    protected $source = '';

    protected static $availableSources = [
        Input::SOURCE_CLI,
        Input::SOURCE_GET,
        Input::SOURCE_POST,
        Input::SOURCE_SESSION,
    ];

    public function setEntry(string $source, string $key, string $limitationKey)
    {
        $this->source = $this->availableSource($source);
        $this->key = $key;
        $this->limitationKey = $limitationKey;
        return $this;
    }

    protected function availableSource(string $source): string
    {
        return in_array($source, static::$availableSources) ? $source : $this->source;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getDefaultLimitation()
    {
        return null;
    }

    public function getEntries(): Traversable
    {
        yield from [];
    }
}
