<?php

namespace kalanis\kw_menu\Traits;


/**
 * Trait TEscape
 * @package kalanis\kw_menu\Traits
 * Escape special characters in classical meta files
 */
trait TEscape
{
    /** @var array<string, string> */
    protected static $escapeNlTr = [
        '|' => '---!!::SEP::!!---',
        "\r\n" => '---!!::CRLF::!!---',
        "\r" => '---!!::CR::!!---',
        "\n" => '---!!::NL::!!---',
    ];

    protected function escapeNl(string $str): string
    {
        return strtr($str, static::$escapeNlTr);
    }

    protected function restoreNl(string $str): string
    {
        return strtr($str, array_flip(static::$escapeNlTr));
    }
}
