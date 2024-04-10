<?php

namespace kalanis\kw_input\Filtered;


use kalanis\kw_input\Inputs;


/**
 * Class Variables
 * @package kalanis\kw_input\Filtered
 * Helping class for passing info from inputs into objects
 */
class Variables extends EntryArrays
{
    public function __construct(Inputs $inputs)
    {
        parent::__construct($inputs->getAllEntries());
    }
}
