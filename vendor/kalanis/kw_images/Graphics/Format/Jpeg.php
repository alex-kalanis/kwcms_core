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
        $this->setImLang($lang);
        if (!function_exists('imagecreatefromjpeg') || !function_exists('imagejpeg')) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getImLang()->imImageMagicLibNotPresent());
        }
        // @codeCoverageIgnoreEnd
    }

    public function load(string $path)
    {
        $result = imagecreatefromjpeg($path);
        if (false === $result) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getImLang()->imCannotCreateFromResource());
        }
        // @codeCoverageIgnoreEnd
        return $result;
    }

    public function save(?string $path, $resource): void
    {
        if (!imagejpeg($resource, $path)) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getImLang()->imCannotSaveResource());
        }
        // @codeCoverageIgnoreEnd
    }
}
