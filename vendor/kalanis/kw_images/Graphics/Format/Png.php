<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;


/**
 * Class Png
 * Portable network graphics format
 * @package kalanis\kw_images\Graphics\Format
 */
class Png extends AFormat
{
    /**
     * @throws ImagesException
     */
    public function __construct()
    {
        if (!function_exists('imagecreatefrompng') || !function_exists('imagepng')) {
            throw new ImagesException('ImageMagic not installed!');
        }
    }

    public function load(string $path)
    {
        $result = imagecreatefrompng($path);
        if (false === $result) {
            throw new ImagesException('Cannot create image from resource!');
        }
        return $result;
    }

    public function save(string $path, $resource): void
    {
        if (!imagepng($resource, $path)) {
            throw new ImagesException('Cannot save image resource!');
        }
    }
}
