<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;
use kalanis\kw_images\TLang;


/**
 * Class AFormat
 * @package kalanis\kw_images\Graphics\Format
 */
abstract class AFormat
{
    use TLang;

    /**
     * @param string $path
     * @throws ImagesException
     * @return \GdImage|resource
     */
    abstract public function load(string $path);

    /**
     * @param string $path
     * @param \GdImage|resource $resource
     * @throws ImagesException
     */
    abstract public function save(string $path, $resource): void;
}
