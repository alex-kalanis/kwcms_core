<?php

namespace kalanis\kw_routed_paths\Sources;


/**
 * Class ASource
 * @package kalanis\kw_routed_paths\Params\Request
 * What is necessary to set the initial data
 */
abstract class ASource
{
    /**
     * @return array<string|int, string|float|int|bool|array<string>> $params
     */
    abstract public function getData(): array;
}
