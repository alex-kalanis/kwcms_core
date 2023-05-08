<?php

namespace kalanis\kw_mime\Check\Traits;


use kalanis\kw_mime\MimeException;


/**
 * Trait TCheckCalls
 * @package kalanis\kw_mime\Check\Traits
 * Check some parts of the system - some thing might not be available on selected instance
 */
trait TCheckCalls
{
    use TLang;

    /**
     * @throws MimeException
     */
    public function checkMimeClass(): void
    {
        if (!$this->isMimeClass()) {
            // @codeCoverageIgnoreStart
            throw new MimeException($this->getMiLang()->miNoClass());
        }
        // @codeCoverageIgnoreEnd
    }

    public function isMimeClass(): bool
    {
        return class_exists('\finfo');
    }

    /**
     * @throws MimeException
     */
    public function checkMimeMethod(): void
    {
        if (!$this->isMimeMethod()) {
            // @codeCoverageIgnoreStart
            throw new MimeException($this->getMiLang()->miNoMethod());
        }
        // @codeCoverageIgnoreEnd
    }

    public function isMimeMethod(): bool
    {
        return method_exists('\finfo', 'buffer');
    }

    /**
     * @throws MimeException
     */
    public function checkMimeFunction(): void
    {
        if (!$this->isMimeFunction()) {
            // @codeCoverageIgnoreStart
            throw new MimeException($this->getMiLang()->miNoFunction());
        }
        // @codeCoverageIgnoreEnd
    }

    protected function isMimeFunction(): bool
    {
        return function_exists('mime_content_type');
    }
}
