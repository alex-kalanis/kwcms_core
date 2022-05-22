<?php

namespace kalanis\kw_menu\MetaSource;


/**
 * Trait TEscape
 * @package kalanis\kw_menu\MetaSource
 * Escape special characters in classical meta files
 */
trait TEscape
{
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
