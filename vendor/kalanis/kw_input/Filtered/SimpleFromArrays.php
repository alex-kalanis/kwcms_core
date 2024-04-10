<?php

namespace kalanis\kw_input\Filtered;


use kalanis\kw_input\Interfaces;
use kalanis\kw_input\Traits\TFill;


/**
 * Class SimpleFromArrays
 * @package kalanis\kw_input\Filtered
 * Helping class for passing info from simple arrays into objects
 */
class SimpleFromArrays extends AFromArrays
{
    use TFill;

    /**
     * @param array<int|string, mixed|null> $inputs
     * @param string $source
     */
    public function __construct(array $inputs, string $source = Interfaces\IEntry::SOURCE_EXTERNAL)
    {
        parent::__construct($this->fillFromEntries($source, $inputs));
    }
}
