<?php

namespace kalanis\kw_images\Interfaces;


/**
 * Interface ISizes
 * @package kalanis\kw_images\Interfaces
 */
interface ISizes
{
    public function getMaxWidth(): int;

    public function getMaxHeight(): int;

    public function getMaxSize(): int;

    public function getTempPrefix(): string;
}
