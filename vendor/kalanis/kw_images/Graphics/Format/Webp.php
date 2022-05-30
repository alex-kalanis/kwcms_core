<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;


/**
 * Class Webp
 * @package kalanis\kw_images\Graphics\Format
 * for some strange reason travisci automatic tests cannot use webp format in their GD library
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
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getLang()->imImageMagicLibNotPresent());
        }
        // @codeCoverageIgnoreEnd
    }

    public function load(string $path)
    {
        $result = imagecreatefromwebp($path);
        if (false === $result) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getLang()->imCannotCreateFromResource());
        }
        // @codeCoverageIgnoreEnd
        return $result;
    }

    public function save(string $path, $resource): void
    {
        if (!imagewebp($resource, $path)) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getLang()->imCannotSaveResource());
        }
        // @codeCoverageIgnoreEnd
    }
}
