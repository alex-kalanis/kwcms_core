<?php

namespace kalanis\kw_lookup\Entries;


use kalanis\kw_lookup\Interfaces\IEntry;


/**
 * Class AEntry
 * @package kalanis\kw_lookup\Entries
 * Simple entry of config
 */
abstract class AEntry implements IEntry
{
    protected $key = '';
    protected $defaultLimit = '';
    protected $limitationKey = '';

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLimitationKey(): string
    {
        return $this->limitationKey;
    }

    public function getDefaultLimitation()
    {
        return $this->defaultLimit;
    }
}
