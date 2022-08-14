<?php

namespace kalanis\kw_cache\Format;


use kalanis\kw_cache\Interfaces\IFormat;


/**
 * Class Serialized
 * @package kalanis\kw_cache\Format
 * Serialize content in storage
 */
class Serialized implements IFormat
{
    public function unpack($content)
    {
        return unserialize(strval($content));
    }

    public function pack($data)
    {
        return serialize($data);
    }
}
