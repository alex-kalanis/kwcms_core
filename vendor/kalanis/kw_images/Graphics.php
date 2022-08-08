<?php

namespace kalanis\kw_images;


use kalanis\kw_images\Graphics\TSizes;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\Interfaces\ISizes;
use kalanis\kw_mime\MimeType;
use kalanis\kw_paths\Stuff;


/**
 * Class Graphics
 * Main image itself
 * @package kalanis\kw_images\Graphics
 */
class Graphics
{
    use TSizes;
    use TLang;

    /** @var Graphics\Processor */
    protected $libGraphics = null;
    /** @var ISizes */
    protected $libSizes = null;
    /** @var MimeType */
    protected $libMime = null;

    public function __construct(Graphics\Processor $libGraphics, MimeType $libMime, ?IIMTranslations $lang = null)
    {
        $this->setLang($lang);
        $this->libGraphics = $libGraphics;
        $this->libMime = $libMime;
    }

    public function setSizes(ISizes $sizes): self
    {
        $this->libSizes = $sizes;
        return $this;
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
        if ($this->libSizes->getMaxSize() < $size) {
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
        $type = $this->getType($realName);
        $this->libGraphics->load($type, $tempPath);
        $sizes = $this->calculateSize(
            $this->libGraphics->width(),
            $this->libSizes->getMaxWidth(),
            $this->libGraphics->height(),
            $this->libSizes->getMaxHeight()
        );
        $this->libGraphics->resample($sizes['width'], $sizes['height']);
        $this->libGraphics->save($type, $tempPath);
        return true;
    }

    /**
     * @param string $path
     * @throws ImagesException
     * @return string
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
}
