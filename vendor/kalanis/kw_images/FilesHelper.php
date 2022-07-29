<?php

namespace kalanis\kw_images;


use kalanis\kw_files\Extended\Config;
use kalanis\kw_files\Extended\Processor;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Processing\Volume\ProcessDir;
use kalanis\kw_files\Processing\Volume\ProcessFile;
use kalanis\kw_files\Processing\Volume\ProcessNode;
use kalanis\kw_images\Interfaces\IIMTranslations;
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
     * @param array<string, string|int> $params
     * @param IIMTranslations|null $langIm
     * @param IFLTranslations|null $langFl
     * @return Files
     * @throws ImagesException
     */
    public static function get(string $webRootDir, array $params = [], ?IIMTranslations $langIm = null, ?IFLTranslations $langFl = null): Files
    {
        $libProcessor = new Processor(
            new ProcessDir($webRootDir, $langFl),
            new ProcessFile($webRootDir, $langFl),
            new ProcessNode($webRootDir),
            (new Config())->setData($params)
        );
        $libGraphics = new Graphics(new Graphics\Format\Factory(), new MimeType(), $langIm);
        $thumbConf = (new Graphics\ThumbConfig())->setData($params);
        return new Files(
            new Graphics\Processor($libGraphics, $thumbConf, $langIm),
            new Files\Image($libProcessor, new Graphics\Processor($libGraphics, (new Graphics\ImageConfig())->setData($params), $langIm), $langIm),
            new Files\Thumb($libProcessor, new Graphics\Processor($libGraphics, $thumbConf, $langIm), $thumbConf, $langIm),
            new Files\Desc($libProcessor, $langIm),
            new Files\DirDesc($libProcessor, $langIm),
            new Files\DirThumb($libProcessor, (new Graphics\DirConfig())->setData($params), $langIm)
        );
    }
}
