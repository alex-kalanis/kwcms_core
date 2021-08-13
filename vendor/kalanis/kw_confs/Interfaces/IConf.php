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
     * @return string
     */
    public function getConfName(): string;

    /**
     * @return string[]
     */
    public function getSettings(): array;
}
