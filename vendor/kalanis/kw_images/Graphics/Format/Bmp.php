<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;


/**
 * Class Bmp
 * Bitmap format
 * @package kalanis\kw_images\Graphics\Format
 */
class Bmp extends AFormat
{
    /**
     * @param IIMTranslations|null $lang
     * @throws ImagesException
     */
    public function __construct(?IIMTranslations $lang = null)
    {
        $this->setImLang($lang);
        if (!function_exists('imagecreatefrombmp') || !function_exists('imagebmp')) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getImLang()->imImageMagicLibNotPresent());
        }
        // @codeCoverageIgnoreEnd
    }

    public function load(string $path)
    {
        $result = imagecreatefrombmp($path);
        if (false === $result) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getImLang()->imCannotCreateFromResource());
        }
        // @codeCoverageIgnoreEnd
        return $result;
    }

    public function save(?string $path, $resource): void
    {
        if (!imagebmp($resource, $path)) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getImLang()->imCannotSaveResource());
        }
        // @codeCoverageIgnoreEnd
    }
}
