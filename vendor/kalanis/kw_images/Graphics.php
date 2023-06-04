<?php

namespace kalanis\kw_images;


use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\Interfaces\ISizes;
use kalanis\kw_images\Traits\TSizes;
use kalanis\kw_images\Traits\TType;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\MimeException;


/**
 * Class Graphics
 * Main image itself
 * @package kalanis\kw_images\Graphics
 */
class Graphics
{
    use TType;
    use TSizes;

    /** @var Graphics\Processor */
    protected $libGraphics = null;
    /** @var IMime */
    protected $libMime = null;
    /** @var ISizes|null */
    protected $libSizes = null;

    public function __construct(Graphics\Processor $libGraphics, IMime $libMime, ?IIMTranslations $lang = null)
    {
        $this->initType($libMime, $lang);
        $this->libGraphics = $libGraphics;
    }

    public function setSizes(ISizes $sizes): self
    {
        $this->libSizes = $sizes;
        return $this;
    }

    /**
     * @param string $tempPath path to temp file
     * @throws ImagesException
     * @return bool
     */
    public function check(string $tempPath): bool
    {
        if (!$this->libSizes) {
            throw new ImagesException($this->getImLang()->imSizesNotSet());
        }
        $size = @filesize($tempPath);
        if (false === $size) {
            throw new ImagesException($this->getImLang()->imImageSizeExists());
        }
        if ($this->libSizes->getMaxSize() < $size) {
            throw new ImagesException($this->getImLang()->imImageSizeTooLarge());
        }
        return true;
    }

    /**
     * @param string $tempPath path to temp file which will be loaded and saved
     * @param string[] $realSourceName real file name for extension detection of source image
     * @param string[]|null $realTargetName real file name for extension detection of target image
     * @throws ImagesException
     * @throws MimeException
     * @return bool
     */
    public function resize(string $tempPath, array $realSourceName, ?array $realTargetName = null): bool
    {
        if (!$this->libSizes) {
            throw new ImagesException($this->getImLang()->imSizesNotSet());
        }
        $realTargetName = is_null($realTargetName) ? $realSourceName : $realTargetName;
        $this->libGraphics->load($this->getType($realSourceName), $tempPath);
        $sizes = $this->calculateSize(
            $this->libGraphics->width(),
            $this->libSizes->getMaxWidth(),
            $this->libGraphics->height(),
            $this->libSizes->getMaxHeight()
        );
        $this->libGraphics->resample($sizes['width'], $sizes['height']);
        $this->libGraphics->save($this->getType($realTargetName), $tempPath);
        return true;
    }
}
