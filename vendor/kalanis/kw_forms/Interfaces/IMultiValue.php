<?php

namespace kalanis\kw_forms\Interfaces;


/**
 * Interface IMultiValue
 * @package kalanis\kw_forms\Interfaces
 * When control can access multiple values
 */
interface IMultiValue
{
    public function getValues(): array;

    public function setValues(array $data): void;
}
