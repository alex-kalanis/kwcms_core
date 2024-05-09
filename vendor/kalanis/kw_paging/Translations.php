<?php

namespace kalanis\kw_paging;


use kalanis\kw_paging\Interfaces\IPGTranslations;


/**
 * Class Translations
 * @package kalanis\kw_paging
 * Translations
 */
class Translations implements IPGTranslations
{
    public function kpgShowResults(int $from, int $to, int $max): string
    {
        return sprintf(
            'Showing results %d - %d of total %d',
            $from,
            $to,
            $max
        );
    }
}
