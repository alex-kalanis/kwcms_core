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
    /** @var array<string, string> */
    protected $types = [
        'bmp' => Bmp::class,
        'gif' => Gif::class,
        'jpeg' => Jpeg::class,
        'jpg' => Jpeg::class,
        'png' => Png::class,
        'wbmp' => Wbmp::class,
        'webp' => Webp::class,
        'avif' => Avif::class,
        'xbm' => Xbm::class,
    ];

    /**
     * @param string $type
     * @param IIMTranslations $lang
     * @throws ImagesException
     * @return AFormat
     */
    public function getByType(string $type, IIMTranslations $lang): AFormat
    {
        if (!isset($this->types[$type])) {
            throw new ImagesException($lang->imUnknownType($type));
        }
        $class = $this->types[$type];
        $instance = new $class($lang);
        if (!$instance instanceof AFormat) {
            throw new ImagesException($lang->imWrongInstance($class));
        }
        return $instance;
    }
}
