<?php

namespace kalanis\kw_confs\Interfaces;


/**
 * Class IConf
 * @package kalanis\kw_confs\Interfaces
 * Configuration available in class
 */
interface IConf
{
    /**
     * @param  string $part
     */
    public function setPart(string $part): void;

    /**
     * @return string
     */
    public function getConfName(): string;

    /**
     * @return array<int|string, int|string|float|bool|null>
     */
    public function getSettings(): array;
}
