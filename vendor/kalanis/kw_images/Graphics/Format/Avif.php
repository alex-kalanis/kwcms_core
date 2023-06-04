<?php

namespace kalanis\kw_images\Graphics\Format;


use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;


/**
 * Class Avif
 * AV1 Image File Format
 * @package kalanis\kw_images\Graphics\Format
 * @codeCoverageIgnore too much hassle to run tests on this one
 *
 * phpunit says:
1) ContentTests\FormatTest::testContentAvif
imageavif(): AVIF image support has been disabled
 */
class Avif extends AFormat
{
    /**
     * @param IIMTranslations|null $lang
     * @throws ImagesException
     */
    public function __construct(?IIMTranslations $lang = null)
    {
        $this->setImLang($lang);
        if (!function_exists('imagecreatefromavif') || !function_exists('imageavif')) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getImLang()->imImageMagicLibNotPresent());
        }
        // @codeCoverageIgnoreEnd
    }

    public function load(string $path)
    {
        $result = imagecreatefromavif($path);
        if (false === $result) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getImLang()->imCannotCreateFromResource());
        }
        // @codeCoverageIgnoreEnd
        return $result;
    }

    public function save(?string $path, $resource): void
    {
        if (!imageavif($resource, $path)) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getImLang()->imCannotSaveResource());
        }
        // @codeCoverageIgnoreEnd
    }
}
