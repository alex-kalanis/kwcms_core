<?php

namespace kalanis\kw_modules\Interfaces\Lists\File;


/**
 * Class IParamFormat
 * @package kalanis\kw_modules\Interfaces\Lists\File
 * Format of params of each record which will be stored
 */
interface IParamFormat
{
    /**
     * @param array<string|int, string|int|float|bool|array<string|int>> $data
     * @return string data to storage
     */
    public function pack(array $data): string;

    /**
     * @param string $content data from storage
     * @return array<string|int, string|int|float|bool|array<string|int>>
     */
    public function unpack(string $content): array;
}
