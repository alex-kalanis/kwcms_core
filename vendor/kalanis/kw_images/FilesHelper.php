<?php

namespace kalanis\kw_images;


use kalanis\kw_extras\ExtendDir;
use kalanis\kw_mime\MimeType;


/**
 * Class Files
 * Operations over files
 * @package kalanis\kw_images
 */
class FilesHelper
{
    /**
     * @param string $webRootDir
     * @return Files
     * @throws ImagesException
     */
    public static function get(string $webRootDir): Files
    {
        $libExtDir = new ExtendDir($webRootDir);
        $libGraphics = new Graphics(new Graphics\Format\Factory(), new MimeType());
        return new Files(new Files\Image($libExtDir, $libGraphics), new Files\Thumb($libExtDir, $libGraphics), new Files\Desc($libExtDir), new Files\DirDesc($libExtDir));
    }
}
