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
        $this->setLang($lang);
        if (!function_exists('imagecreatefromwbmp') || !function_exists('imagewbmp')) {
            throw new ImagesException($this->getLang()->imImageMagicLibNotPresent());
        }
    }

    public function load(string $path)
    {
        $result = imagecreatefromwbmp($path);
        if (false === $result) {
            throw new ImagesException($this->getLang()->imCannotCreateFromResource());
        }
        return $result;
    }

    public function save(string $path, $resource): void
    {
        if (!imagewbmp($resource, $path)) {
            throw new ImagesException($this->getLang()->imCannotSaveResource());
        }
    }
}
