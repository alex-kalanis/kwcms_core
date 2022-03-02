<?php

namespace kalanis\kw_images;


use kalanis\kw_paths\Extras\ExtendDir;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_mime\MimeType;
use kalanis\kw_paths\Interfaces\IPATranslations;


/**
 * Class Files
 * Operations over files
 * @package kalanis\kw_images
 */
class FilesHelper
{
    /**
     * @param string $webRootDir
     * @param array $params
     * @param IIMTranslations|null $langIm
     * @param IPATranslations|null $langPa
     * @return Files
     * @throws ImagesException
     */
    public static function get(string $webRootDir, array $params = [], ?IIMTranslations $langIm = null, ?IPATranslations $langPa = null): Files
    {
        $libExtDir = new ExtendDir($webRootDir, $langPa);
        $libGraphics = new Graphics(new Graphics\Format\Factory(), new MimeType(), $langIm);
        return new Files(
            new Files\Image($libExtDir, $libGraphics, $params, $langIm),
            new Files\Thumb($libExtDir, $libGraphics, $params, $langIm),
            new Files\Desc($libExtDir, $langIm),
            new Files\DirDesc($libExtDir, $langIm),
            new Files\DirThumb($libExtDir, $libGraphics, $params, $langIm)
        );
    }
}
