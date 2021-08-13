<?php

namespace kalanis\kw_input\Interfaces;


/**
 * Interface ISource
 * @package kalanis\kw_input\Interfaces
 * Source of values to parse
 */
interface ISource
{
    public function cli(): ?array;

    public function get(): ?array;

    public function post(): ?array;

    public function files(): ?array;

    public function cookie(): ?array;

    public function session(): ?array;

    public function server(): ?array;

    public function env(): ?array;

    public function external(): ?array;
}
