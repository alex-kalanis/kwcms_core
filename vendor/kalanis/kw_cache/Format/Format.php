<?php

namespace kalanis\kw_cache\Format;


use kalanis\kw_cache\Interfaces\IFormat;


/**
 * Class Format
 * @package kalanis\kw_cache\Format
 * Basic work with content to storage - let primitives stay, encode rest
 */
class Format implements IFormat
{
    public function unpack($content)
    {
        if (is_numeric($content)) {
            return $content;
        }
        if (is_bool($content)) {
            return $content;
        }
        $encodeResult = json_decode(strval($content), true);
        if (is_null($encodeResult)) {
            // problems with decoding - return original string
            return $content;
        }
        return $encodeResult;
    }

    public function pack($data)
    {
        if (is_bool($data)) {
            return $data;
        }
        if (is_numeric($data)) {
            return $data;
        }
        if (is_string($data)) {
            return $data;
        }
        return strval(json_encode($data));
    }
}
