<?php

namespace kalanis\kw_paths;


use kalanis\kw_paths\Interfaces\IPATranslations;


/**
 * Class Translations
 * @package kalanis\kw_paths
 * Translations
 */
class Translations implements IPATranslations
{
    public function paNoDirectoryDelimiterSet(): string
    {
        return 'No directory delimiter set!';
    }
}
