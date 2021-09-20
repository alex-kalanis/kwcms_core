<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;


/**
 * Class Wbmp
 * Wap bitmap image
 * @package kalanis\kw_images\Graphics\Format
 */
class Wbmp extends AFormat
{
    /**
     * @throws ImagesException
     */
    public function __construct()
    {
        if (!function_exists('imagecreatefromwbmp') || !function_exists('imagewbmp')) {
            throw new ImagesException('ImageMagic not installed!');
        }
    }

    public function load(string $path)
    {
        $result = imagecreatefromwbmp($path);
        if (false === $result) {
            throw new ImagesException('Cannot create image from resource!');
        }
        return $result;
    }

    public function save(string $path, $resource): void
    {
        if (!imagewbmp($resource, $path)) {
            throw new ImagesException('Cannot save image resource!');
        }
    }
}
