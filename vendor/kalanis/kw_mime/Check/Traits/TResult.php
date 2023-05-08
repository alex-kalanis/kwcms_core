<?php

namespace kalanis\kw_mime\Check\Traits;


/**
 * Trait TResult
 * @package kalanis\kw_mime\Check\Traits
 */
trait TResult
{
    /**
     * @param mixed $mime
     * @return string
     */
    public function determineResult($mime): string
    {
        return is_string($mime) ? strval($mime) : 'application/octet-stream';
    }
}
