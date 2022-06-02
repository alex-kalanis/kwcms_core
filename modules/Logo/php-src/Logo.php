<?php

namespace KWCMS\modules\Logo;


use kalanis\kw_confs\Config;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Linking\InternalLink;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_modules\Output\Raw;
use kalanis\kw_paths\Stored;


/**
 * Class Logo
 * @package KWCMS\modules\Logo
 * Site logo
 */
class Logo extends AModule
{
    protected $mime = null;
    protected $extLink = null;
    protected $intLink = null;

    public function __construct()
    {
        Config::load(static::getClassName(static::class));
        $this->mime = new MimeType(true);
        $this->extLink = new ExternalLink(Stored::getPath());
        $this->intLink = new InternalLink(Stored::getPath());
    }

    public function process(): void
    {
    }

    public function output(): AOutput
    {
        return ($this->params[ISitePart::KEY_LEVEL] == ISitePart::SITE_RESPONSE) ? $this->outContent() : $this->outTemplate() ;
    }

    protected function outContent(): AOutput
    {
        $out = new Raw();
        $imagePath = $this->intLink->userContent(Config::get('Logo', 'path'));
        if (!$imagePath) {
            $imagePath = realpath(implode(DIRECTORY_SEPARATOR, [
                __DIR__, '..', 'images', 'logo.png'
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

    protected function outTemplate(): AOutput
    {
        $out = new Html();
        $tmpl = new Template();
        return $out->setContent($tmpl->setData(
            $this->extLink->linkVariant(null, 'Logo', true)
        )->render());
    }
}
