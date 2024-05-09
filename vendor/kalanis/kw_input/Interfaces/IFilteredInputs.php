<?php

namespace kalanis\kw_input\Interfaces;


use ArrayAccess;
use IteratorAggregate;

/**
 * Interface IFilteredInputs
 * @package kalanis\kw_input\Interfaces
 * Filtered inputs available for processing
 * @extends ArrayAccess<string|int, mixed|null>
 * @extends IteratorAggregate<string, mixed|null>
 */
interface IFilteredInputs extends ArrayAccess, IteratorAggregate
{}
