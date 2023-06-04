<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;


/**
 * Class Wbmp
 * Wap bitmap image
 * @package kalanis\kw_images\Graphics\Format
 */
class Wbmp extends AFormat
{
    /**
     * @param IIMTranslations|null $lang
     * @throws ImagesException
     */
    public function __construct(?IIMTranslations $lang = null)
    {
        $this->setImLang($lang);
        if (!function_exists('imagecreatefromwbmp') || !function_exists('imagewbmp')) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getImLang()->imImageMagicLibNotPresent());
        }
        // @codeCoverageIgnoreEnd
    }

    public function load(string $path)
    {
        $result = imagecreatefromwbmp($path);
        if (false === $result) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getImLang()->imCannotCreateFromResource());
        }
        // @codeCoverageIgnoreEnd
        return $result;
    }

    public function save(?string $path, $resource): void
    {
        if (!imagewbmp($resource, $path)) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getImLang()->imCannotSaveResource());
        }
        // @codeCoverageIgnoreEnd
    }
}
