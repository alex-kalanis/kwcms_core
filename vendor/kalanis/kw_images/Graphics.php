<?php

namespace kalanis\kw_images;


use kalanis\kw_mime\MimeType;
use kalanis\kw_paths\Stuff;


/**
 * Class Graphics
 * @package kalanis\kw_images
 */
class Graphics
{
    protected $factory = null;
    protected $libMime = null;
    protected $resource = null;

    /**
     * @param Graphics\Format\Factory $factory
     * @param MimeType $libMime
     * @throws ImagesException
     */
    public function __construct(Graphics\Format\Factory $factory, MimeType $libMime)
    {
        if (!(function_exists('imagecreatetruecolor')
            && function_exists('imagecolorallocate')
            && function_exists('imagesetpixel')
            && function_exists('imagecopyresized')
            && function_exists('imagecopyresampled')
            && function_exists('imagesx')
            && function_exists('imagesy')
        )) {
            throw new ImagesException('GD2 library is not present!');
        }

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
        $processor = $this->factory->getByType($this->getType($path));
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
        $processor = $this->factory->getByType($this->getType($path));
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
            throw new ImagesException(sprintf('Wrong file mime type - got *%s*', $mime));
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
            imagedestroy($resource);
            throw new ImagesException('Image cannot be resized!');
        }
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
            imagedestroy($resource);
            throw new ImagesException('Image cannot be resampled!');
        }
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
        if (false === $resource)
            throw new ImagesException('Cannot create empty image!');
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
            throw new ImagesException('Cannot access image size!');
        }
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
            throw new ImagesException('Cannot access image size!');
        }
        return intval($size);
    }

    /**
     * @throws ImagesException
     */
    protected function checkResource(): void
    {
        if (empty($this->resource)) {
            throw new ImagesException('You must load image first!');
        }
    }
}
