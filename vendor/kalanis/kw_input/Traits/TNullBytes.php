<?php

namespace kalanis\kw_input\Traits;


/**
 * Trait TNullBytes
 * @package kalanis\kw_input\Traits
 * Remove null bytes from string
 */
trait TNullBytes
{
    /**
     * @param string $string
     * @return string
     */
    public function removeNullBytes($string)
    {
        return str_replace(chr(0), '', $string);
    }
}
