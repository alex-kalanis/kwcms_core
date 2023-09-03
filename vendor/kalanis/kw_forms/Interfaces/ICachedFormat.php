<?php

namespace kalanis\kw_forms\Interfaces;


/**
 * Class ICachedFormat
 * @package kalanis\kw_forms\Interfaces
 * Format of params of each record which will be stored
 */
interface ICachedFormat
{
    /**
     * @param array<string, string|int|float|bool|null> $data
     * @return string data to storage
     */
    public function pack(array $data): string;

    /**
     * @param string $content data from storage
     * @return array<string, string|int|float|bool|null>
     */
    public function unpack(string $content): array;
}
