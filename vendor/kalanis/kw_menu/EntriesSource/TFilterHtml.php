<?php

namespace kalanis\kw_menu\EntriesSource;


/**
 * Trait TFilterHtml
 * @package kalanis\kw_menu\EntriesSource
 */
trait TFilterHtml
{
    protected static $allowedExtensions = ['htm', 'html', 'xhtm', 'xhtml'];

    public function filterExt(string $ext): bool
    {
        return in_array($ext, static::$allowedExtensions);
    }
}
