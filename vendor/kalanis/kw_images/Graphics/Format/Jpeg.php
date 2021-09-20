<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;


/**
 * Class Jpeg
 * JPEG images
 * @package kalanis\kw_images\Graphics\Format
 */
class Jpeg extends AFormat
{
    /**
     * @throws ImagesException
     */
    public function __construct()
    {
        if (!function_exists('imagecreatefromjpeg') || !function_exists('imagejpeg')) {
            throw new ImagesException('ImageMagic not installed!');
        }
    }

    public function load(string $path)
    {
        $result = imagecreatefromjpeg($path);
        if (false === $result) {
            throw new ImagesException('Cannot create image from resource!');
        }
        return $result;
    }

    public function save(string $path, $resource): void
    {
        if (!imagejpeg($resource, $path)) {
            throw new ImagesException('Cannot save image resource!');
        }
    }
}
