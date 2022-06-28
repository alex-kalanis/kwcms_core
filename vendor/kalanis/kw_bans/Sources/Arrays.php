<?php

namespace kalanis\kw_bans\Sources;


/**
 * Class Arrays
 * @package kalanis\kw_bans\Sources
 * Bans source is array
 */
class Arrays extends ASources
{
    /**
     * @param array<int, string> $sources
     */
    public function __construct(array $sources)
    {
        $this->knownRecords = $sources;
    }
}
