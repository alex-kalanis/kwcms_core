<?php

namespace kalanis\kw_paths\Extras;


use kalanis\kw_files\Processing\TPathTransform;


/**
 * Class PathTransform
 * @package kalanis\kw_paths\Extras
 * Just implementation of transformation of names
 */
class PathTransform
{
    use TPathTransform;

    public static function get(): self
    {
        return new static();
    }
}
