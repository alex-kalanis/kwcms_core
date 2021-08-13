<?php

namespace KWCMS\modules\Watermark;


use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\InternalLink;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\DumpingCallback;
use kalanis\kw_modules\Output\Raw;
use kalanis\kw_paths\Stuff;


/**
 * Class Watermark
 * @package KWCMS\modules\Watermark
 * Watermark over images
 */
class Watermark extends AModule
{
    /** @var MimeType */
    protected $mime = null;
    /** @var InternalLink */
    protected $link = null;
    protected $imagePath = '';
    protected $repeat = false;

    public function __construct()
    {
        Config::load(static::getClassName(static::class));
        $this->mime = new MimeType(true);
        $this->link = new InternalLink(Config::getPath());
    }

    public function process(): void
    {
        $repeat = $this->inputs->getInArray('repeat', [
            IEntry::SOURCE_CLI, IEntry::SOURCE_POST, IEntry::SOURCE_GET
        ]);
        $this->repeat = !empty($repeat);
        $this->imagePath = Config::getPath()->getPath();
    }

    public function output(): AOutput
    {
        if ($this->params[ISitePart::KEY_LEVEL] != ISitePart::SITE_RESPONSE) {
            $out = new Raw();
            return $out->setContent('Wrong module run level for watermark image!');
        }
        $out = new DumpingCallback();
        return $out->setCallback([$this, 'createImage']);
    }

    protected function createImage()
    {
        $rWatermark = null;
        $rImage = null;

        try {
            # create image with watermark
            $image = $this->link->userContent();
            $watermark = $this->link->userContent('watermark.png');
            if (!$watermark) {
                $watermark = $this->link->moduleContent('Watermark','watermark.png');
            }

            $rWatermark = $this->openImage($watermark);
            $rImage = $this->openImage($image);

            imagecopy($rImage, $rWatermark, (imagesx($rImage)/2)-(imagesx($rWatermark)/2), (imagesy($rImage)/2)-(imagesy($rWatermark)/2), 0, 0, imagesx($rWatermark), imagesy($rWatermark));

            if ($this->repeat) {
                $waterless = imagesx($rImage) - imagesx($rWatermark);
                $rest = ceil($waterless/imagesx($rWatermark)/2);

                for ($n=1; $n<=$rest; $n++) {
                    imagecopy($rImage, $rWatermark, ((imagesx($rImage)/2)-(imagesx($rWatermark)/2))-(imagesx($rWatermark)*$n), (imagesy($rImage)/2)-(imagesy($rWatermark)/2), 0, 0, imagesx($rWatermark), imagesy($rWatermark));
                    imagecopy($rImage, $rWatermark, ((imagesx($rImage)/2)-(imagesx($rWatermark)/2))+(imagesx($rWatermark)*$n), (imagesy($rImage)/2)-(imagesy($rWatermark)/2), 0, 0, imagesx($rWatermark), imagesy($rWatermark));
                }
            }

            header("Last-Modified: " . gmdate('D, d M Y H:i:s T', filemtime($image)) );
            header("Content-Type: " . $this->mime->mimeByPath($this->imagePath));
            header('Content-Disposition: filename="' . Stuff::filename($this->imagePath). '"');

            $this->dumpImage($rImage, $image);

            return '';
        } catch (ModuleException $ex) {
            return $ex->getMessage();

        } finally {
            if ($rWatermark) {
                imagedestroy($rWatermark);
            }
            if ($rImage) {
                imagedestroy($rImage);
            }
        }
    }

    /**
     * @param string $image
     * @return resource
     * @throws ModuleException
     */
    protected function openImage(string $image)
    {
        $ext = strtolower(Stuff::fileExt($image));
        $resource = null;
        if ('jpg' == $ext || 'jpeg' == $ext) {
            $resource = @imagecreatefromjpeg($image);
        } elseif ('png' == $ext) {
            $resource = @imagecreatefrompng($image);
        } elseif ('gif' == $ext) {
            $resource = @imagecreatefromgif($image);
        } elseif ('bmp' == $ext) {
            $resource = @imagecreatefrombmp($image);
        } else {
            throw new ModuleException(sprintf('No allowed image format for %s !', $image));
        }
        if (empty($resource)) {
            throw new ModuleException(sprintf('Error opening %s !', $image));
        }
        return $resource;
    }

    /**
     * @param resource $resource
     * @param string $image
     * @return bool
     * @throws ModuleException
     */
    protected function dumpImage($resource, string $image): bool
    {
        $ext = strtolower(Stuff::fileExt($image));
        if ('jpg' == $ext || 'jpeg' == $ext) {
            return imagejpeg($resource,null,95);
        } elseif ('png' == $ext) {
            return imagepng($resource, null, 95);
        } elseif ('gif' == $ext) {
            return imagegif($resource);
        } elseif ('bmp' == $ext) {
            return imagebmp($resource);
        } else {
            throw new ModuleException(sprintf('No allowed image format for %s !', $image));
        }
    }
}
