<?php

namespace kalanis\kw_images;


use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_mime\MimeType;
use kalanis\kw_paths\Stuff;


/**
 * Class Graphics
 * @package kalanis\kw_images
 * @todo: pass images from storage to temp and then back - cannot work with images in storages directly
 */
class Graphics
{
    use TLang;

    protected $factory = null;
    protected $libMime = null;
    protected $resource = null;

    /**
     * @param Graphics\Format\Factory $factory
     * @param MimeType $libMime
     * @param IIMTranslations|null $lang
     * @throws ImagesException
     */
    public function __construct(Graphics\Format\Factory $factory, MimeType $libMime, ?IIMTranslations $lang = null)
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
        $this->libMime = $libMime;
    }

    /**
     * @param string $path
     * @return $this
     * @throws ImagesException
     */
    public function load(string $path): self
    {
        $processor = $this->factory->getByType($this->getType($path), $this->getLang());
        $this->resource = $processor->load($path);
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     * @throws ImagesException
     */
    public function save(string $path): self
    {
        $this->checkResource();
        $processor = $this->factory->getByType($this->getType($path), $this->getLang());
        $processor->save($path, $this->resource);
        return $this;
    }

    /**
     * @param string $path
     * @return string
     * @throws ImagesException
     */
    protected function getType(string $path): string
    {
        $mime = $this->libMime->mimeByExt(Stuff::fileExt($path));
        list($type, $app) = explode('/', $mime);
        if ('image' != $type) {
            throw new ImagesException($this->getLang()->imWrongMime($mime));
        }
        return $app;
    }

    /**
     * Change image size
     * @param int|null $width
     * @param int|null $height
     * @return $this
     * @throws ImagesException
     */
    public function resize(?int $width = null, ?int $height = null): self
    {
        $this->checkResource();
        $fromWidth = $this->width();
        $fromHeight = $this->height();
        $width = (!is_null($width) && ($width > 0)) ? (int)$width : $fromWidth;
        $height = (!is_null($height) && ($height > 0)) ? (int)$height : $fromHeight;
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
     * Change image size and update sample
     * @param int|null $width
     * @param int|null $height
     * @return $this
     * @throws ImagesException
     */
    public function resample(?int $width = null, ?int $height = null)
    {
        $this->checkResource();
        $fromWidth = $this->width();
        $fromHeight = $this->height();
        $width = ($width && is_numeric($width) && ($width > 0)) ? (int)$width : $fromWidth;
        $height = ($height && is_numeric($height) && ($height > 0)) ? (int)$height : $fromHeight;
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
     * @return resource
     * @throws ImagesException
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
     * @return int
     * @throws ImagesException
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
     * @return int
     * @throws ImagesException
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
