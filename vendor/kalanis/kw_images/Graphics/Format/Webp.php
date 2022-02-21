<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;


/**
 * Class Webp
 * @package kalanis\kw_images\Graphics\Format
 */
class Webp extends AFormat
{
    /**
     * @param IIMTranslations|null $lang
     * @throws ImagesException
     */
    public function __construct(?IIMTranslations $lang = null)
    {
        $this->setLang($lang);
        if (!function_exists('imagecreatefromwebp') || !function_exists('imagewebp')) {
            throw new ImagesException($this->getLang()->imImageMagicLibNotPresent());
        }
    }

    public function load(string $path)
    {
        $result = imagecreatefromwebp($path);
        if (false === $result) {
            throw new ImagesException($this->getLang()->imCannotCreateFromResource());
        }
        return $result;
    }

    public function save(string $path, $resource): void
    {
        if (!imagewebp($resource, $path)) {
            throw new ImagesException($this->getLang()->imCannotSaveResource());
        }
    }
}
