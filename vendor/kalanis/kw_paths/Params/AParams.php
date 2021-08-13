<?php

namespace kalanis\kw_paths\Params;


/**
 * Class AParams
 * @package kalanis\kw_paths\Params
 * Parsed params from another source, usually QUERY_STRING
 *
 * It's been meant to set it inside the Inputs/Source/Basic as external one.
 *
 */
abstract class AParams
{
    protected $params = [];

    public function process(): self
    {
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    protected function preset(array $params): void
    {
        $this->params = $params;
    }
}
