<?php

namespace kalanis\kw_routed_paths\Sources;


/**
 * Class Arrays
 * @package kalanis\kw_routed_paths\Sources
 * Input source is Array which provides the path data
 */
class Arrays extends ASource
{
    /** @var array<string, bool|int|float|string|array<string>> */
    protected $input = [];

    /**
     * @param array<string, bool|int|float|string|array<string>> $inputs
     */
    public function __construct(array $inputs)
    {
        $this->input = $inputs;
    }

    public function getData(): array
    {
        return $this->input;
    }
}
