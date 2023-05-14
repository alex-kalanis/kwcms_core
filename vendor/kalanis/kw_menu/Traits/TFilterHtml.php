<?php

namespace kalanis\kw_menu\Traits;


/**
 * Trait TFilterHtml
 * @package kalanis\kw_menu\Traits
 */
trait TFilterHtml
{
    /** @var string[] */
    protected static $allowedExtensions = ['htm', 'html', 'xhtm', 'xhtml'];

    public function filterExt(string $ext): bool
    {
        return in_array($ext, static::$allowedExtensions);
    }
}
