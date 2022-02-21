<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;


/**
 * Class Jpeg
 * JPEG images
 * @package kalanis\kw_images\Graphics\Format
 */
class Jpeg extends AFormat
{
    /**
     * @param IIMTranslations|null $lang
     * @throws ImagesException
     */
    public function __construct(?IIMTranslations $lang = null)
    {
        $this->setLang($lang);
        if (!function_exists('imagecreatefromjpeg') || !function_exists('imagejpeg')) {
            throw new ImagesException($this->getLang()->imImageMagicLibNotPresent());
        }
    }

    public function load(string $path)
    {
        $result = imagecreatefromjpeg($path);
        if (false === $result) {
            throw new ImagesException($this->getLang()->imCannotCreateFromResource());
        }
        return $result;
    }

    public function save(string $path, $resource): void
    {
        if (!imagejpeg($resource, $path)) {
            throw new ImagesException($this->getLang()->imCannotSaveResource());
        }
    }
}
