<?php

namespace KWCMS\modules\Sysimage\Controllers;


use kalanis\kw_mime\MimeException;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Raw;
use kalanis\kw_modules\Processing\Support;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;


/**
 * Class Sysimage
 * @package KWCMS\modules\Sysimage\Controllers
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

    /**
     * @throws PathsException
     */
    public function process(): void
    {
        $path = Stored::getPath();
        $pathArray = StoreRouted::getPath()->getPath();
        $module = array_shift($pathArray);
        $this->imagePath = realpath(implode(DIRECTORY_SEPARATOR, [
            $path->getDocumentRoot() . $path->getPathToSystemRoot(), 'modules', Support::normalizeModuleName($module), 'images', Stuff::arrayToPath($pathArray)
        ]));
        if (!$this->imagePath) {
            $this->imagePath = realpath(implode(DIRECTORY_SEPARATOR,
                array_merge([
                    __DIR__, '..', '..', 'images'
                ], $pathArray)
            ));
        }
        if (!$this->imagePath) {
            $this->imagePath = realpath(implode(DIRECTORY_SEPARATOR, [
                __DIR__, '..', '..', 'images', 'no_image_available.png'
            ]));
        }
    }

    /**
     * @throws PathsException
     * @throws MimeException
     * @return AOutput
     */
    public function output(): AOutput
    {
        if ($this->params[ISitePart::KEY_LEVEL] != ISitePart::SITE_RESPONSE) {
            $out = new Raw();
            return $out->setContent('Wrong module run level for watermark image!');
        }

        $out = new Raw();
        $content = @file_get_contents($this->imagePath);
        if ($content) {
            header('Content-Type: ' . $this->mime->mimeByPath($this->imagePath));
        } else {
            $content = 'Problem with selected image and its backup!';
        }
        return $out->setContent($content);
    }
}
