<?php

namespace kalanis\kw_bans\Sources;


/**
 * Class ASources
 * @package kalanis\kw_bans\Sources
 * Bans sources
 */
abstract class ASources
{
    /** @var string[] */
    protected $knownRecords = [];

    public function getRecords(): array
    {
        return $this->knownRecords;
    }
}
