<?php

namespace kalanis\kw_input\Filtered;


use IteratorAggregate;
use kalanis\kw_input\Interfaces;
use kalanis\kw_input\Traits\TFill;


/**
 * Class ArrayAccessed
 * @package kalanis\kw_input\Filtered
 * Helping class for passing info from simple arrays into objects
 */
class ArrayAccessed extends AFromArrays
{
    use TFill;

    /**
     * @param IteratorAggregate<string|int, mixed|null> $inputs
     * @param string $source
     */
    public function __construct(IteratorAggregate $inputs, string $source = Interfaces\IEntry::SOURCE_EXTERNAL)
    {
        parent::__construct($this->fillFromIterator($source, $inputs));
    }
}
