<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use ReflectionClass;
use ReflectionException;


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
        $className = $this->types[$type];

        try {
            /** @var class-string $className */
            $ref = new ReflectionClass($className);
            $instance = $ref->newInstance($lang);
            if (!$instance instanceof AFormat) {
                throw new ImagesException($lang->imWrongInstance($className));
            }
            return $instance;
        } catch (ReflectionException $ex) {
            throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
