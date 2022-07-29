<?php

namespace kalanis\kw_images\Graphics;


use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\Interfaces\ISizes;
use kalanis\kw_images\TLang;


/**
 * Class Image
 * Main image itself
 * @package kalanis\kw_images\Graphics
 */
class Processor
{
    use TSizes;
    use TLang;

    /** @var Graphics */
    protected $libGraphics = null;
    /** @var ISizes */
    protected $libConf = null;

    public function __construct(Graphics $libGraphics, ISizes $config, ?IIMTranslations $lang = null)
    {
        $this->libGraphics = $libGraphics;
        $this->libConf = $config;
        $this->setLang($lang);
    }

    /**
     * @param string $path path to temp file
     * @throws ImagesException
     */
    public function check(string $path): void
    {
        $size = @filesize($path);
        if (is_null($size)) {
            throw new ImagesException($this->getLang()->imImageSizeExists());
        }
        if ($this->libConf->getMaxSize() < $size) {
            throw new ImagesException($this->getLang()->imImageSizeTooLarge());
        }
    }

    /**
     * @param string $tempPath path to temp file
     * @param string $realName real file name for extension detection
     * @throws ImagesException
     * @return bool
     */
    public function resize(string $tempPath, string $realName): bool
    {
        $this->libGraphics->load($realName, $tempPath);
        $sizes = $this->calculateSize(
            $this->libGraphics->width(),
            $this->libConf->getMaxWidth(),
            $this->libGraphics->height(),
            $this->libConf->getMaxHeight()
        );
        $this->libGraphics->resample($sizes['width'], $sizes['height']);
        $this->libGraphics->save($realName, $tempPath);
        return true;
    }
}
