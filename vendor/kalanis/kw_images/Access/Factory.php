<?php

namespace kalanis\kw_images\Access;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Extended\Config;
use kalanis\kw_files\Extended\Processor;
use kalanis\kw_images\Configs;
use kalanis\kw_images\Content\BasicOperations;
use kalanis\kw_images\Content\Dirs;
use kalanis\kw_images\Content\Images;
use kalanis\kw_images\Content\ImageUpload;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\Traits\TLang;
use kalanis\kw_images\Content;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\Sources;
use kalanis\kw_mime\Check;
use kalanis\kw_mime\Interfaces\IMime;


/**
 * Class Files
 * Operations over files
 * @package kalanis\kw_images\Access
 */
class Factory
{
    use TLang;

    protected CompositeAdapter $composite;
    protected Configs\ConfigFactory $configFactory;
    protected Config $fileConfig;
    protected IMime $mime;

    public function __construct(
        CompositeAdapter $compositeAdapter,
        ?IMime $mime = null,
        ?Configs\ConfigFactory $configFactory = null,
        ?Config $fileConfig = null,
        ?IIMTranslations $imLang = null
    )
    {
        $this->setImLang($imLang);
        $this->composite = $compositeAdapter;
        $this->configFactory = $configFactory ?: new Configs\ConfigFactory();
        $this->fileConfig = $fileConfig ?: new Config();
        $this->mime = $mime ?: new Check\CustomList();
    }

    /**
     * @param array<string, string|int> $params
     * @return BasicOperations
     */
    public function getOperations(array $params = []): BasicOperations
    {
        $fileConf = $this->fileConfig->setData($params);
        return new BasicOperations(  // operations with images
            new Sources\Image($this->composite, $fileConf, $this->getImLang()),
            new Sources\Thumb($this->composite, $fileConf, $this->getImLang()),
            new Sources\Desc($this->composite, $fileConf, $this->getImLang())
        );
    }

    /**
     * @param array<string, string|int> $params
     * @throws ImagesException
     * @return Dirs
     */
    public function getDirs(array $params = []): Dirs
    {
        $this->configFactory->setData($params);
        $fileConf = $this->fileConfig->setData($params);
        return new Dirs(
            new Content\ImageSize(
                new Graphics(
                    new Graphics\Processor(
                        new Graphics\Format\Factory(),
                        $this->getImLang()
                    ),
                    $this->mime,
                    $this->getImLang()
                ),
                $this->configFactory->getThumb(),
                new Sources\Image($this->composite, $fileConf, $this->getImLang()),
                $this->getImLang()
            ),
            new Sources\Thumb($this->composite, $fileConf, $this->getImLang()),
            new Sources\DirDesc($this->composite, $fileConf, $this->getImLang()),
            new Sources\DirThumb($this->composite, $fileConf, $this->getImLang()),
            new Processor($this->composite, $fileConf),
            $this->getImLang()
        );
    }

    /**
     * @param array<string, string|int> $params
     * @throws ImagesException
     * @return Images
     */
    public function getImages(array $params = []): Images
    {
        $this->configFactory->setData($params);
        $fileConf = $this->fileConfig->setData($params);
        $image = new Sources\Image($this->composite, $fileConf, $this->getImLang());
        return new Images(
            new Content\ImageSize(
                new Graphics(
                    new Graphics\Processor(
                        new Graphics\Format\Factory(),
                        $this->getImLang()
                    ),
                    $this->mime,
                    $this->getImLang()
                ),
                $this->configFactory->getThumb(),
                $image,
                $this->getImLang()
            ),
            $image,
            new Sources\Thumb($this->composite, $fileConf, $this->getImLang()),
            new Sources\Desc($this->composite, $fileConf, $this->getImLang())
        );
    }

    /**
     * @param array<string, string|int> $params
     * @throws ImagesException
     * @return ImageUpload
     */
    public function getUpload(array $params = []): ImageUpload
    {
        $this->configFactory->setData($params);
        $fileConf = $this->fileConfig->setData($params);
        $graphics = new Graphics(
            new Graphics\Processor(
                new Graphics\Format\Factory(),
                $this->getImLang()
            ),
            $this->mime,
            $this->getImLang()
        );
        $image = new Sources\Image($this->composite, $fileConf, $this->getImLang());
        return new ImageUpload(  // process uploaded images
            $graphics,
            $image,
            $this->configFactory->getImage(),
            new Images(
                new Content\ImageSize(
                    $graphics,
                    $this->configFactory->getThumb(),
                    $image,
                    $this->getImLang()
                ),
                new Sources\Image($this->composite, $fileConf, $this->getImLang()),
                new Sources\Thumb($this->composite, $fileConf, $this->getImLang()),
                new Sources\Desc($this->composite, $fileConf, $this->getImLang())
            ),
            $this->configFactory->getProcessor()
        );
    }
}
