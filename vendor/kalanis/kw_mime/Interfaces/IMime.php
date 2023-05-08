<?php

namespace kalanis\kw_mime\Interfaces;


use kalanis\kw_mime\MimeException;


/**
 * Interface IMime
 * @package kalanis\kw_mime\Interfaces
 * Get the mime info about something in that path
 * Interface to separate necessary dependencies
 */
interface IMime
{
    /**
     * @param string[] $path
     * @throws MimeException
     * @return string
     */
    public function getMime(array $path): string;
}
