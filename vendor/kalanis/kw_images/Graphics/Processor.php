<?php

namespace kalanis\kw_images\Graphics;


use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\TLang;


/**
 * Class Processor
 * @package kalanis\kw_images
 * Pass images in temporary local storage  - cannot work with images directly in main storage
 */
class Processor
{
    use TLang;

    /** @var Format\Factory */
    protected $factory = null;
    /** @var resource|\GdImage|null */
    protected $resource = null;

    /**
     * @param Format\Factory $factory
     * @param IIMTranslations|null $lang
     * @throws ImagesException
     */
    public function __construct(Format\Factory $factory, ?IIMTranslations $lang = null)
    {
        $this->setLang($lang);

        if (!(function_exists('imagecreatetruecolor')
            && function_exists('imagecolorallocate')
            && function_exists('imagesetpixel')
            && function_exists('imagecopyresized')
            && function_exists('imagecopyresampled')
            && function_exists('imagesx')
            && function_exists('imagesy')
        )) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getLang()->imGdLibNotPresent());
        }
        // @codeCoverageIgnoreEnd

        $this->factory = $factory;
    }

    /**
     * @param string $type
     * @param string $tempPath
     * @throws ImagesException
     * @return $this
     */
    public function load(string $type, string $tempPath): self
    {
        $processor = $this->factory->getByType($type, $this->getLang());
        $this->resource = $processor->load($tempPath);
        return $this;
    }

    /**
     * @param string $type
     * @param string $tempPath
     * @throws ImagesException
     * @return $this
     */
    public function save(string $type, string $tempPath): self
    {
        $this->checkResource();
        $processor = $this->factory->getByType($type, $this->getLang());
        $processor->save($tempPath, $this->resource);
        return $this;
    }

    /**
     * Change image size - cut it to desired size
     * @param int|null $width
     * @param int|null $height
     * @throws ImagesException
     * @return $this
     */
    public function resize(?int $width = null, ?int $height = null): self
    {
        $this->checkResource();
        $fromWidth = $this->width();
        $fromHeight = $this->height();
        $width = (!is_null($width) && (0 < $width)) ? intval($width) : $fromWidth;
        $height = (!is_null($height) && (0 < $height)) ? intval($height) : $fromHeight;
        $resource = $this->create($width, $height);
        if (false === imagecopyresized($resource, $this->resource, 0, 0, 0, 0, $width, $height, $fromWidth, $fromHeight)) {
            // @codeCoverageIgnoreStart
            imagedestroy($resource);
            throw new ImagesException($this->getLang()->imImageCannotResize());
        }
        // @codeCoverageIgnoreEnd
        imagedestroy($this->resource);
        $this->resource = $resource;
        return $this;
    }

    /**
     * Change image size - content will change its proportions according the passed sizes
     * @param int|null $width
     * @param int|null $height
     * @throws ImagesException
     * @return $this
     */
    public function resample(?int $width = null, ?int $height = null)
    {
        $this->checkResource();
        $fromWidth = $this->width();
        $fromHeight = $this->height();
        $width = (!is_null($width) && (0 < $width)) ? intval($width) : $fromWidth;
        $height = (!is_null($height) && (0 < $height)) ? intval($height) : $fromHeight;
        $resource = $this->create($width, $height);
        if (false === imagecopyresampled($resource, $this->resource, 0, 0, 0, 0, $width, $height, $fromWidth, $fromHeight)) {
            // @codeCoverageIgnoreStart
            imagedestroy($resource);
            throw new ImagesException($this->getLang()->imImageCannotResample());
        }
        // @codeCoverageIgnoreEnd
        imagedestroy($this->resource);
        $this->resource = $resource;
        return $this;
    }

    /**
     * Create empty image resource
     * @param int $width
     * @param int $height
     * @throws ImagesException
     * @return \GdImage|resource
     */
    protected function create(int $width, int $height)
    {
        $resource = imagecreatetruecolor($width, $height);
        if (false === $resource) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getLang()->imImageCannotCreateEmpty());
        }
        // @codeCoverageIgnoreEnd
        return $resource;
    }

    /**
     * @throws ImagesException
     * @return int
     */
    public function width(): int
    {
        $this->checkResource();
        $size = imagesx($this->resource);
        if (false === $size) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getLang()->imImageCannotGetSize());
        }
        // @codeCoverageIgnoreEnd
        return intval($size);
    }

    /**
     * @throws ImagesException
     * @return int
     */
    public function height(): int
    {
        $this->checkResource();
        $size = imagesy($this->resource);
        if (false === $size) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getLang()->imImageCannotGetSize());
        }
        // @codeCoverageIgnoreEnd
        return intval($size);
    }

    /**
     * @throws ImagesException
     */
    protected function checkResource(): void
    {
        if (empty($this->resource)) {
            throw new ImagesException($this->getLang()->imImageLoadFirst());
        }
    }
}
