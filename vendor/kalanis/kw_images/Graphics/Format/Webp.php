<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;


/**
 * Class Webp
 * @package kalanis\kw_images\Graphics\Format
 */
class Webp extends AFormat
{
    /**
     * @throws ImagesException
     */
    public function __construct()
    {
        if (!function_exists('imagecreatefromwebp') || !function_exists('imagewebp')) {
            throw new ImagesException('ImageMagic not installed!');
        }
    }

    public function load(string $path)
    {
        $result = imagecreatefromwebp($path);
        if (false === $result) {
            throw new ImagesException('Cannot create image from resource!');
        }
        return $result;
    }

    public function save(string $path, $resource): void
    {
        if (!imagewebp($resource, $path)) {
            throw new ImagesException('Cannot save image resource!');
        }
    }
}
