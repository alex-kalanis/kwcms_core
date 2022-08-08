<?php

namespace kalanis\kw_images;


use kalanis\kw_files\Extended\Config;
use kalanis\kw_files\Extended\Processor;
use kalanis\kw_files\CompositeProcessor;
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
        $libComposite = (new CompositeProcessor())->setData(
            new ProcessDir($webRootDir, $langFl),
            new ProcessFile($webRootDir, $langFl),
            new ProcessNode($webRootDir)
        );
        $fileConf = (new Config())->setData($params);
        $libProcessor = new Processor( ## extend dir props
            $libComposite,
            $fileConf
        );
        $libGraphics = new Graphics\Processor(new Graphics\Format\Factory(), $langIm);
        $thumbConf = (new Graphics\ThumbConfig())->setData($params);
        return new Files(  ## process images
            (new Graphics($libGraphics, new MimeType(), $langIm))->setSizes($thumbConf),
            new Sources\Image($libComposite, $fileConf, $langIm),
            new Sources\Thumb($libComposite, $fileConf, $langIm),
            new Sources\Desc($libComposite, $fileConf, $langIm),
            new Sources\DirDesc($libComposite, $fileConf, $langIm),
            new Sources\DirThumb($libComposite, $fileConf, $langIm)
        );
    }
}
