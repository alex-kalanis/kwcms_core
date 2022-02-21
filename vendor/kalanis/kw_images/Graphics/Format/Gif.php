<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;


/**
 * Class Gif
 * Graphics Interchange Format (GIF)
 * @package kalanis\kw_images\Graphics\Format
 */
class Gif extends AFormat
{
    /**
     * @param IIMTranslations|null $lang
     * @throws ImagesException
     */
    public function __construct(?IIMTranslations $lang = null)
    {
        $this->setLang($lang);
        if (!function_exists('imagecreatefromgif') || !function_exists('imagegif')) {
            throw new ImagesException($this->getLang()->imImageMagicLibNotPresent());
        }
    }

    public function load(string $path)
    {
        $result = imagecreatefromgif($path);
        if (false === $result) {
            throw new ImagesException($this->getLang()->imCannotCreateFromResource());
        }
        return $result;
    }

    public function save(string $path, $resource): void
    {
        if (!imagegif($resource, $path)) {
            throw new ImagesException($this->getLang()->imCannotSaveResource());
        }
    }
}
