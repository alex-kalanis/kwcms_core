<?php

namespace kalanis\kw_modules\Mixer\PassedParams;


/**
 * Class SingleParam
 * @package kalanis\kw_modules\Mixer\PassedParams
 * Pass param as first element of array
 */
class SingleParam extends APassedParam
{
    public function change(string $content): array
    {
        return [$content];
    }
}
