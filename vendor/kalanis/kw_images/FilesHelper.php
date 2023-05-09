<?php

namespace kalanis\kw_images;


use kalanis\kw_files\Access\Factory as access_factory;
use kalanis\kw_files\Extended\Config;
use kalanis\kw_files\Extended\Processor;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_images\Content\BasicOperations;
use kalanis\kw_images\Content\Dirs;
use kalanis\kw_images\Content\Images;
use kalanis\kw_images\Content\ImageUpload;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_mime\Check\CustomList;
use kalanis\kw_paths\PathsException;


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
     * @throws FilesException
     * @throws PathsException
     * @return BasicOperations
     */
    public static function getOperations(string $webRootDir, array $params = [], ?IIMTranslations $langIm = null, ?IFLTranslations $langFl = null): BasicOperations
    {
        $fileConf = (new Config())->setData($params);
        $compositeFactory = new access_factory($langFl);
        $libProcess = $compositeFactory->getClass($webRootDir);
        return new BasicOperations(  // operations with images
            new Sources\Image($libProcess, $fileConf, $langIm),
            new Sources\Thumb($libProcess, $fileConf, $langIm),
            new Sources\Desc($libProcess, $fileConf, $langIm),
        );
    }

    /**
     * @param string $webRootDir
     * @param array<string, string|int> $params
     * @param IIMTranslations|null $langIm
     * @param IFLTranslations|null $langFl
     * @throws FilesException
     * @throws ImagesException
     * @throws PathsException
     * @return Dirs
     */
    public static function getDirs(string $webRootDir, array $params = [], ?IIMTranslations $langIm = null, ?IFLTranslations $langFl = null): Dirs
    {
        $fileConf = (new Config())->setData($params);
        $compositeFactory = new access_factory($langFl);
        $libProcess = $compositeFactory->getClass($webRootDir);
        return new Dirs(
            new Content\ImageSize(
                new Graphics(new Graphics\Processor(new Graphics\Format\Factory(), $langIm), new CustomList(), $langIm),
                (new Graphics\ThumbConfig())->setData($params),
                new Sources\Image($libProcess, $fileConf, $langIm),
                $langIm
            ),
            new Sources\Thumb($libProcess, $fileConf, $langIm),
            new Sources\DirDesc($libProcess, $fileConf, $langIm),
            new Sources\DirThumb($libProcess, $fileConf, $langIm),
            new Processor($libProcess, $fileConf),
        );
    }

    /**
     * @param string $webRootDir
     * @param array<string, string|int> $params
     * @param IIMTranslations|null $langIm
     * @param IFLTranslations|null $langFl
     * @throws FilesException
     * @throws ImagesException
     * @throws PathsException
     * @return Images
     */
    public static function getImages(string $webRootDir, array $params = [], ?IIMTranslations $langIm = null, ?IFLTranslations $langFl = null): Images
    {
        $fileConf = (new Config())->setData($params);
        $compositeFactory = new access_factory($langFl);
        $libProcess = $compositeFactory->getClass($webRootDir);
        $image = new Sources\Image($libProcess, $fileConf, $langIm);
        return new Images(
            new Content\ImageSize(
                new Graphics(new Graphics\Processor(new Graphics\Format\Factory(), $langIm), new CustomList(), $langIm),
                (new Graphics\ThumbConfig())->setData($params),
                $image,
                $langIm
            ),
            $image,
            new Sources\Thumb($libProcess, $fileConf, $langIm),
            new Sources\Desc($libProcess, $fileConf, $langIm),
        );
    }

    /**
     * @param string $webRootDir
     * @param array<string, string|int> $params
     * @param IIMTranslations|null $langIm
     * @param IFLTranslations|null $langFl
     * @throws FilesException
     * @throws ImagesException
     * @throws PathsException
     * @return ImageUpload
     */
    public static function getUpload(string $webRootDir, array $params = [], ?IIMTranslations $langIm = null, ?IFLTranslations $langFl = null): ImageUpload
    {
        $fileConf = (new Config())->setData($params);
        $compositeFactory = new access_factory($langFl);
        $libProcess = $compositeFactory->getClass($webRootDir);
        $graphics = new Graphics(new Graphics\Processor(new Graphics\Format\Factory(), $langIm), new CustomList(), $langIm);
        $image = new Sources\Image($libProcess, $fileConf, $langIm);
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
                new Sources\Image($libProcess, $fileConf, $langIm),
                new Sources\Thumb($libProcess, $fileConf, $langIm),
                new Sources\Desc($libProcess, $fileConf, $langIm),
            )
        );
    }
}
