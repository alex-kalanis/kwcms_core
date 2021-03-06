<?php

namespace KWCMS\modules\Icon;


use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Linking\InternalLink;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_paths\Stored;


/**
 * Class Icon
 * @package KWCMS\modules\Icon
 * Icon images
 */
class Icon extends AModule
{
    protected $mime = null;
    protected $imagePath = '';
    /** @var UserDir|null */
    protected $userDir = null;
    /** @var ExternalLink|null */
    protected $libExternal = null;
    /** @var InternalLink|null */
    protected $libInternal = null;

    public function __construct()
    {
        $this->mime = new MimeType(true);
        $this->userDir = new UserDir(Stored::getPath());
        $this->libExternal = new ExternalLink(Stored::getPath());
        $this->libInternal = new InternalLink(Stored::getPath());
    }

    public function process(): void
    {
    }

    public function output(): Output\AOutput
    {
        return ($this->params[ISitePart::KEY_LEVEL] == ISitePart::SITE_RESPONSE) ? $this->outResponse() : $this->outLink() ;
    }

    protected function outLink(): Output\AOutput
    {
        $presetPath = $this->getFromParam('path');
        $template = new HeadTemplate();
        $out = new Output\Html();
        return $out->setContent($template->setData($this->libExternal->linkVariant($presetPath, 'icon', true))->render());
    }

    protected function outResponse(): Output\AOutput
    {
        $imagePath = $this->libInternal->userContent(Stored::getPath()->getPath());
        if (!$imagePath) {
            $imagePath = realpath(implode(DIRECTORY_SEPARATOR, [
                __DIR__, '..', 'images', 'no_image_available.png'
            ]));
        }

        $out = new Output\Raw();
        $content = @file_get_contents($imagePath);
        if ($content) {
            header('Content-Type: ' . $this->mime->mimeByPath($this->imagePath));
        } else {
            $content = 'Problem with selected image and its backup!';
        }
        return $out->setContent($content);
    }
}
