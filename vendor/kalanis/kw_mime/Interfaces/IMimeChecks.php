<?php

namespace kalanis\kw_mime\Interfaces;


/**
 * Interface IMimeChecks
 * @package kalanis\kw_mime\Interfaces
 * Interface for visitor pattern
 */
interface IMimeChecks extends IMime
{
    /**
     * @param mixed $source
     * @return bool
     */
    public function canUse($source): bool;
}
