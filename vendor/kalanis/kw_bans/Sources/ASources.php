<?php

namespace kalanis\kw_bans\Sources;


/**
 * Class ASources
 * @package kalanis\kw_bans\Sources
 * Bans sources
 */
abstract class ASources
{
    /** @var array<int, string> */
    protected array $knownRecords = [];

    /**
     * @return array<int, string>
     */
    public function getRecords(): array
    {
        return $this->knownRecords;
    }
}
