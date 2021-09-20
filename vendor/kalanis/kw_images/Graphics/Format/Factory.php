<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;


/**
 * Class Factory
 * @package kalanis\kw_images\Graphics\Format
 */
class Factory
{
    protected $types = [
        'bmp' => '\kalanis\kw_images\Graphics\Format\Bmp',
        'gif' => '\kalanis\kw_images\Graphics\Format\Gif',
        'jpeg' => '\kalanis\kw_images\Graphics\Format\Jpeg',
        'jpg' => '\kalanis\kw_images\Graphics\Format\Jpeg',
        'png' => '\kalanis\kw_images\Graphics\Format\Png',
        'wbmp' => '\kalanis\kw_images\Graphics\Format\Wbmp',
        'webp' => '\kalanis\kw_images\Graphics\Format\Webp',
    ];

    /**
     * @param string $type
     * @return AFormat
     * @throws ImagesException
     */
    public function getByType(string $type): AFormat
    {
        if (!isset($this->types[$type])) {
            throw new ImagesException(sprintf('Unknown type *%s*', $type));
        }
        $class = $this->types[$type];
        return new $class();
    }
}
