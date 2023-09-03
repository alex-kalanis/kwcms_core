<?php

namespace kalanis\kw_forms\Cache\Formats;


use kalanis\kw_forms\Interfaces\ICachedFormat;


/**
 * Class Serialize
 * @package kalanis\kw_forms\Cache\Formats
 */
class Serialize implements ICachedFormat
{
    public function pack(array $data): string
    {
        return serialize($data);
    }

    public function unpack(string $content): array
    {
        $data = @ unserialize($content);
        return (false === $data) ? [] : $data ;
    }
}
