<?php

namespace kalanis\kw_images;


use kalanis\kw_images\Interfaces\IExifConstants;
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

    protected Graphics\Processor $libGraphics;
    protected ?ISizes $libSizes = null;

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
     * @param string[] $realSourceName real file name for extension detection of source image
     * @throws ImagesException
     * @throws MimeException
     * @return bool
     */
    public function check(string $tempPath, array $realSourceName): bool
    {
        $this->getLibSizes();
        $size = @filesize($tempPath);
        if (false === $size) {
            throw new ImagesException($this->getImLang()->imImageSizeExists());
        }
        if ($this->getLibSizes()->getMaxFileSize() < $size) {
            throw new ImagesException($this->getImLang()->imImageSizeTooLarge());
        }
        $this->libGraphics->load($this->getType($realSourceName), $tempPath);
        if ($this->getLibSizes()->getMaxInHeight() < $this->libGraphics->height()) {
            throw new ImagesException($this->getImLang()->imImageSizeTooLarge());
        }
        if ($this->getLibSizes()->getMaxInWidth() < $this->libGraphics->width()) {
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
        $this->getLibSizes();
        $realTargetName = is_null($realTargetName) ? $realSourceName : $realTargetName;
        $this->libGraphics->load($this->getType($realSourceName), $tempPath);
        $sizes = $this->calculateSize(
            $this->libGraphics->width(),
            $this->getLibSizes()->getMaxStoreWidth(),
            $this->libGraphics->height(),
            $this->getLibSizes()->getMaxStoreHeight()
        );
        $this->libGraphics->resample($sizes['width'], $sizes['height']);
        $this->libGraphics->save($this->getType($realTargetName), $tempPath);
        return true;
    }

    /**
     * @param string $tempPath path to temp file which will be loaded and saved
     * @param string[] $realSourceName real file name for extension detection of source image
     * @param string[]|null $realTargetName real file name for extension detection of target image
     * @throws ImagesException
     * @throws MimeException
     * @return bool
     * @link https://stackoverflow.com/questions/7489742/php-read-exif-data-and-adjust-orientation
     */
    public function orientate(string $tempPath, array $realSourceName, ?array $realTargetName = null): bool
    {
        $exif = @exif_read_data($tempPath);
        if (false === $exif) {
            throw new ImagesException($this->getImLang()->imImageCannotOrientate());
        }
        if (!empty($exif['Orientation']) && in_array($exif['Orientation'], [
                IExifConstants::EXIF_ORIENTATION_NORMAL,
                IExifConstants::EXIF_ORIENTATION_MIRROR_SIMPLE,
                IExifConstants::EXIF_ORIENTATION_UPSIDE_DOWN,
                IExifConstants::EXIF_ORIENTATION_MIRROR_UPSIDE_DOWN,
                IExifConstants::EXIF_ORIENTATION_ON_LEFT,
                IExifConstants::EXIF_ORIENTATION_MIRROR_ON_LEFT,
                IExifConstants::EXIF_ORIENTATION_ON_RIGHT,
                IExifConstants::EXIF_ORIENTATION_MIRROR_ON_RIGHT,
            ])) {
            $realTargetName = is_null($realTargetName) ? $realSourceName : $realTargetName;
            $this->libGraphics->load($this->getType($realSourceName), $tempPath);
            $this->libGraphics->orientate($exif['Orientation']);
            $this->libGraphics->save($this->getType($realTargetName), $tempPath);
            return true;
        }

        return false;
    }

    /**
     * @throws ImagesException
     * @return ISizes
     */
    protected function getLibSizes(): ISizes
    {
        if (empty($this->libSizes)) {
            throw new ImagesException($this->getImLang()->imSizesNotSet());
        }
        return $this->libSizes;
    }
}
