<?php

namespace kalanis\kw_table\core\Interfaces\Table;


/**
 * Interface IFilterMulti
 * @package kalanis\kw_table\core\Interfaces\Table
 * Filter multiple content
 */
interface IFilterMulti
{
    /**
     * @return string[]
     */
    public function getPairs(): array;
}
