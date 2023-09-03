<?php

namespace kalanis\kw_forms\Cache\Formats;


use kalanis\kw_forms\Interfaces\ICachedFormat;


/**
 * Class Json
 * @package kalanis\kw_forms\Cache\Formats
 */
class Json implements ICachedFormat
{
    public function pack(array $data): string
    {
        return strval(json_encode($data));
    }

    public function unpack(string $content): array
    {
        $data = @ json_decode($content, true);
        return (false === $data) ? [] : $data ;
    }
}
