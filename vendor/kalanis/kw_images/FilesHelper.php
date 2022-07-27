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
     * @param array $params
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
            new Config(
                isset($params['desc_dir']) ? $params['desc_dir'] : null,
                isset($params['desc_file']) ? $params['desc_file'] : null,
                isset($params['desc_ext']) ? $params['desc_ext'] : null,
                isset($params['thumb_dir']) ? $params['thumb_dir'] : null
            )
        );
        $libGraphics = new Graphics(new Graphics\Format\Factory(), new MimeType(), $langIm);
        return new Files(
            new Files\Image($libProcessor, $libGraphics, $params, $langIm),
            new Files\Thumb($libProcessor, $libGraphics, $params, $langIm),
            new Files\Desc($libProcessor, $langIm),
            new Files\DirDesc($libProcessor, $langIm),
            new Files\DirThumb($libProcessor, $libGraphics, $params, $langIm)
        );
    }
}
