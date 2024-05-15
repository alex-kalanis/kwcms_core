<?php

namespace kalanis\kw_images\Interfaces;


/**
 * Interface ISizes
 * @package kalanis\kw_images\Interfaces
 */
interface ISizes
{
    public function getMaxInWidth(): int;

    public function getMaxInHeight(): int;

    public function getMaxStoreWidth(): int;

    public function getMaxStoreHeight(): int;

    public function getMaxFileSize(): int;

    public function getTempPrefix(): string;
}
