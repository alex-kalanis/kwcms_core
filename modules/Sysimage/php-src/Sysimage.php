<?php

namespace KWCMS\modules\Sysimage;


use kalanis\kw_confs\Config;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Raw;


/**
 * Class Sysimage
 * @package KWCMS\modules\Sysimage
 * System images
 */
class Sysimage extends AModule
{
    protected $mime = null;
    protected $imagePath = '';

    public function __construct()
    {
        $this->mime = new MimeType(true);
    }

    public function process(): void
    {
        $this->imagePath = realpath(implode(DIRECTORY_SEPARATOR, [
            __DIR__ , '..', 'images', Config::getPath()->getPath()
        ]));
        if (!$this->imagePath) {
            $this->imagePath = realpath(implode(DIRECTORY_SEPARATOR, [
                __DIR__, '..', 'images', 'no_image_available.png'
            ]));
        }
    }

    public function output(): AOutput
    {
        if ($this->params[ISitePart::KEY_LEVEL] != ISitePart::SITE_RESPONSE) {
            $out = new Raw();
            return $out->setContent('Wrong module run level for watermark image!');
        }

        $out = new Raw();
        $content = @file_get_contents($this->imagePath);
        if ($content) {
            header("Content-Type: " . $this->mime->mimeByPath($this->imagePath));
        } else {
            $content = 'Problem with selected image and its backup!';
        }
        return $out->setContent($content);
    }
}
