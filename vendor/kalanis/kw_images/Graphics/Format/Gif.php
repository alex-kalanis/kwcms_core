<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;


/**
 * Class Gif
 * Graphics Interchange Format (GIF)
 * @package kalanis\kw_images\Graphics\Format
 */
class Gif extends AFormat
{
    /**
     * @throws ImagesException
     */
    public function __construct()
    {
        if (!function_exists('imagecreatefromgif') || !function_exists('imagegif')) {
            throw new ImagesException('ImageMagic not installed!');
        }
    }

    public function load(string $path)
    {
        $result = imagecreatefromgif($path);
        if (false === $result) {
            throw new ImagesException('Cannot create image from resource!');
        }
        return $result;
    }

    public function save(string $path, $resource): void
    {
        if (!imagegif($resource, $path)) {
            throw new ImagesException('Cannot save image resource!');
        }
    }
}
