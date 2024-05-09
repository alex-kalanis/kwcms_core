<?php

namespace kalanis\kw_modules\Mixer\PassedParams;


/**
 * Class APassedParam
 * @package kalanis\kw_modules\Mixer\PassedParams
 * What to do with passed params
 */
abstract class APassedParam
{
    /**
     * @param string $content
     * @return array<string|int, string|int|float|bool|array<string|int>>
     */
    abstract public function change(string $content): array;
}
