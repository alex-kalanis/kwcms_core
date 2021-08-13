<?php

namespace KWCMS\modules\Image;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\InternalLink;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_modules\Output\Raw;
use kalanis\kw_paths\Stuff;


/**
 * Class Image
 * @package KWCMS\modules\Image
 * Users images
 */
class Image extends AModule
{
    protected $mime = null;
    protected $extLink = null;
    protected $intLink = null;

    public function __construct()
    {
        Config::load(static::getClassName(static::class));
        Lang::load(static::getClassName(static::class));
        $this->mime = new MimeType(true);
        $this->extLink = new ExternalLink(Config::getPath());
        $this->intLink = new InternalLink(Config::getPath());
    }

    public function process(): void
    {
    }

    public function output(): AOutput
    {
        $path = Config::getPath()->getPath();
        return ($this->params[ISitePart::KEY_LEVEL] == ISitePart::SITE_RESPONSE) ? $this->outContent($path) : $this->outTemplate($path) ;
    }

    protected function outContent(string $path): AOutput
    {
        $out = new Raw();
        $imagePath = $this->intLink->userContent($path);
        if (!$imagePath) {
            $imagePath = realpath(implode(DIRECTORY_SEPARATOR, [
                '..', 'images', 'no_image_available.png'
            ]));
        }
        $content = @file_get_contents($imagePath);
        if ($content) {
            header("Content-Type: " . $this->mime->mimeByPath($imagePath));
        } else {
            $content = 'Problem with selected image and its backup!';
        }
        return $out->setContent($content);
    }

    protected function outTemplate(string $path): AOutput
    {
        $out = new Html();
        $tmpl = new Template();
        return $out->setContent($tmpl->setData(
            $this->imagePath($path),
            $this->descriptionFile($path),
            $this->imageCreated($path)
        )->render());
    }

    protected function imagePath(string $path): string
    {
        $hasWatermark = (bool)Config::get('Image', 'watermark', false);
        $canWatermark = (array)Config::get('Image', 'accept_watermark', []);
        return $hasWatermark && in_array(Stuff::fileExt($path), $canWatermark)
            ? $this->extLink->linkVariant($path, 'watermark', true)
            : $this->extLink->linkStatic($path) ;
    }

    protected function imageCreated(string $path): string
    {
        $dateFormat = Config::get('Image', 'date_format', 'd.m.Y\ \@\ H:i:s');
        $file = $this->intLink->userContent($path);
        return $file
            ? date($dateFormat. filemtime($file))
            : '' ;
    }

    protected function descriptionFile(string $path): string
    {
        $descDir = Config::get('Image', 'desc', '.txt');
        $dir = Stuff::directory($path);
        $file = Stuff::fileBase(Stuff::filename($path));
        $descPath = implode(DIRECTORY_SEPARATOR, [$dir, $descDir, $file . '.dsc']);
        $desc = $this->intLink->userContent($descPath);
        return $desc
            ? @file_get_contents($desc)
            : '' ;
    }
}
