<?php

namespace kalanis\kw_connect\Interfaces;


use kalanis\kw_connect\ConnectException;


/**
 * Interface IRow
 * @package kalanis\kw_connect\Interfaces
 * Access rows in table
 */
interface IRow
{
    /**
     * @param string|int $property
     * @return mixed
     * @throws ConnectException
     */
    public function getValue($property);

    /**
     * @param string|int $name
     * @return bool
     */
    public function __isset($name);
}
