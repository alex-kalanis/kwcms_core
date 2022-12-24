<?php

namespace kalanis\kw_images;


use kalanis\kw_files\Extended\Config;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Processing\Volume;
use kalanis\kw_images\Content\BasicOperations;
use kalanis\kw_images\Content\Dirs;
use kalanis\kw_images\Content\Images;
use kalanis\kw_images\Content\ImageUpload;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_mime\MimeType;


/**
 * Class Files
 * Operations over files
 * @package kalanis\kw_images
 * @codeCoverageIgnore building libraries with volume as main dependency
 */
class FilesHelper
{
    /**
     * @param string $webRootDir
     * @param array<string, string|int> $params
     * @param IIMTranslations|null $langIm
     * @param IFLTranslations|null $langFl
     * @return BasicOperations
     */
    public static function getOperations(string $webRootDir, array $params = [], ?IIMTranslations $langIm = null, ?IFLTranslations $langFl = null): BasicOperations
    {
        $fileConf = (new Config())->setData($params);
        $libProcessFiles = new Volume\ProcessFile($webRootDir, $langFl);
        $libProcessNodes = new Volume\ProcessNode($webRootDir);
        return new BasicOperations(  // operations with images
            new Sources\Image($libProcessNodes, $libProcessFiles, $fileConf, $langIm),
            new Sources\Thumb($libProcessNodes, $libProcessFiles, $fileConf, $langIm),
            new Sources\Desc($libProcessNodes, $libProcessFiles, $fileConf, $langIm),
        );
    }

    /**
     * @param string $webRootDir
     * @param array<string, string|int> $params
     * @param IIMTranslations|null $langIm
     * @param IFLTranslations|null $langFl
     * @throws ImagesException
     * @return Dirs
     */
    public static function getDirs(string $webRootDir, array $params = [], ?IIMTranslations $langIm = null, ?IFLTranslations $langFl = null): Dirs
    {
        $fileConf = (new Config())->setData($params);
        $libProcessFiles = new Volume\ProcessFile($webRootDir, $langFl);
        $libProcessNodes = new Volume\ProcessNode($webRootDir);
        return new Dirs(
            new Content\ImageSize(
                new Graphics(new Graphics\Processor(new Graphics\Format\Factory(), $langIm), new MimeType(), $langIm),
                (new Graphics\ThumbConfig())->setData($params),
                new Sources\Image($libProcessNodes, $libProcessFiles, $fileConf, $langIm),
                $langIm
            ),
            new Sources\Thumb($libProcessNodes, $libProcessFiles, $fileConf, $langIm),
            new Sources\DirDesc($libProcessNodes, $libProcessFiles, $fileConf, $langIm),
            new Sources\DirThumb($libProcessNodes, $libProcessFiles, $fileConf, $langIm),
        );
    }

    /**
     * @param string $webRootDir
     * @param array<string, string|int> $params
     * @param IIMTranslations|null $langIm
     * @param IFLTranslations|null $langFl
     * @throws ImagesException
     * @return Images
     */
    public static function getImages(string $webRootDir, array $params = [], ?IIMTranslations $langIm = null, ?IFLTranslations $langFl = null): Images
    {
        $fileConf = (new Config())->setData($params);
        $libProcessFiles = new Volume\ProcessFile($webRootDir, $langFl);
        $libProcessNodes = new Volume\ProcessNode($webRootDir);
        return new Images(
            new Content\ImageSize(
                new Graphics(new Graphics\Processor(new Graphics\Format\Factory(), $langIm), new MimeType(), $langIm),
                (new Graphics\ThumbConfig())->setData($params),
                new Sources\Image($libProcessNodes, $libProcessFiles, $fileConf, $langIm),
                $langIm
            ),
            new Sources\Thumb($libProcessNodes, $libProcessFiles, $fileConf, $langIm),
            new Sources\Desc($libProcessNodes, $libProcessFiles, $fileConf, $langIm),
        );
    }

    /**
     * @param string $webRootDir
     * @param array<string, string|int> $params
     * @param IIMTranslations|null $langIm
     * @param IFLTranslations|null $langFl
     * @throws ImagesException
     * @return ImageUpload
     */
    public static function getUpload(string $webRootDir, array $params = [], ?IIMTranslations $langIm = null, ?IFLTranslations $langFl = null): ImageUpload
    {
        $libProcessFiles = new Volume\ProcessFile($webRootDir, $langFl);
        $libProcessNodes = new Volume\ProcessNode($webRootDir);
        $fileConf = (new Config())->setData($params);
        $graphics = new Graphics(new Graphics\Processor(new Graphics\Format\Factory(), $langIm), new MimeType(), $langIm);
        $image = new Sources\Image($libProcessNodes, $libProcessFiles, $fileConf, $langIm);
        return new ImageUpload(  // process uploaded images
            $graphics,
            $image,
            (new Graphics\ImageConfig())->setData($params),
            new Images(
                new Content\ImageSize(
                    $graphics,
                    (new Graphics\ThumbConfig())->setData($params),
                    $image,
                    $langIm
                ),
                new Sources\Thumb($libProcessNodes, $libProcessFiles, $fileConf, $langIm),
                new Sources\Desc($libProcessNodes, $libProcessFiles, $fileConf, $langIm),
            )
        );
    }
}
