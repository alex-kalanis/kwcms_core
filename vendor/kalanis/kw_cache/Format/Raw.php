<?php

namespace kalanis\kw_cache\Format;


use kalanis\kw_cache\Interfaces\IFormat;


/**
 * Class Raw
 * @package kalanis\kw_cache\Format
 * No encoding/decoding made
 */
class Raw implements IFormat
{
    public function unpack($content)
    {
        return $content;
    }

    public function pack($data)
    {
        return $data;
    }
}
