<?php

namespace kalanis\kw_cache\Format;


use kalanis\kw_cache\Interfaces;


/**
 * Class Factory
 * @package kalanis\kw_cache\Format
 * Basic implementation of format factory - use just "not so stupid" check
 */
class Factory
{
    public function getFormat(/** @scrutinizer ignore-unused */Interfaces\ICache $cache): Interfaces\IFormat
    {
        return new Format();
    }
}
