<?php

namespace kalanis\kw_modules\Output;


/**
 * Class AOutput
 * @package kalanis\kw_modules
 * Output abstraction
 *
 * possible outputs:
 * - Raw (for images, rss, ...)
 * - HTML
 * - Json
 */
abstract class AOutput
{
    protected $canWrap = false;

    public function __toString()
    {
        return $this->output();
    }

    abstract public function output(): string;

    public final function canWrap(): bool
    {
        return $this->canWrap;
    }
}
