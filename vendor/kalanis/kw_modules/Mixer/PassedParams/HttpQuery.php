<?php

namespace kalanis\kw_modules\Mixer\PassedParams;


use kalanis\kw_paths\Stuff;

/**
 * Class HttpQuery
 * @package kalanis\kw_modules\Mixer\PassedParams
 * Pass params the same way as http query
 */
class HttpQuery extends APassedParam
{
    public function change(string $content): array
    {
        return Stuff::httpStringIntoArray($content);
    }
}
