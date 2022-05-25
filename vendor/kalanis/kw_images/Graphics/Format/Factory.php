<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;


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
        'avif' => '\kalanis\kw_images\Graphics\Format\Avif',
        'xbm' => '\kalanis\kw_images\Graphics\Format\Xbm',
    ];

    /**
     * @param string $type
     * @param IIMTranslations $lang
     * @return AFormat
     * @throws ImagesException
     */
    public function getByType(string $type, IIMTranslations $lang): AFormat
    {
        if (!isset($this->types[$type])) {
            throw new ImagesException($lang->imUnknownType($type));
        }
        $class = $this->types[$type];
        return new $class($lang);
    }
}
