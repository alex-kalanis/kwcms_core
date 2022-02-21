<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;


/**
 * Class Png
 * Portable network graphics format
 * @package kalanis\kw_images\Graphics\Format
 */
class Png extends AFormat
{
    /**
     * @param IIMTranslations|null $lang
     * @throws ImagesException
     */
    public function __construct(?IIMTranslations $lang = null)
    {
        $this->setLang($lang);
        if (!function_exists('imagecreatefrompng') || !function_exists('imagepng')) {
            throw new ImagesException($this->getLang()->imImageMagicLibNotPresent());
        }
    }

    public function load(string $path)
    {
        $result = imagecreatefrompng($path);
        if (false === $result) {
            throw new ImagesException($this->getLang()->imCannotCreateFromResource());
        }
        return $result;
    }

    public function save(string $path, $resource): void
    {
        if (!imagepng($resource, $path)) {
            throw new ImagesException($this->getLang()->imCannotSaveResource());
        }
    }
}
