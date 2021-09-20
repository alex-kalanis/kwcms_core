<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;


/**
 * Class Bmp
 * Bitmap format
 * @package kalanis\kw_images\Graphics\Format
 */
class Bmp extends AFormat
{
    /**
     * @throws ImagesException
     */
    public function __construct()
    {
        if (!function_exists('imagecreatefrombmp') || !function_exists('imagebmp')) {
            throw new ImagesException('ImageMagic not installed or too old!');
        }
    }

    public function load(string $path)
    {
        $result = imagecreatefrombmp($path);
        if (false === $result) {
            throw new ImagesException('Cannot create image from resource!');
        }
        return $result;
    }

    public function save(string $path, $resource): void
    {
        if (!imagebmp($resource, $path)) {
            throw new ImagesException('Cannot save image resource!');
        }
    }
}
