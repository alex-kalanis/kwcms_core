<?php

namespace KWCMS\modules\Icon\Controllers;


use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\Factory as files_factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_images\Access\Factory as images_factory;
use kalanis\kw_images\Content\Images;
use kalanis\kw_images\ImagesException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\MimeException;
use kalanis\kw_mime\Check;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\ExternalLink;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Core\Libs\ImagesTranslations;
use KWCMS\modules\Icon\Libs;


/**
 * Class Icon
 * @package KWCMS\modules\Icon\Controllers
 * Icon images
 */
class Icon extends AModule
{
    use TToString;

    protected IMime $mime;
    protected ArrayPath $arrPath;
    protected ExternalLink $libExternal;
    protected InnerLinks $innerLink;
    protected Images $sources;

    /**
     * @param mixed ...$constructParams
     * @throws FilesException
     * @throws ImagesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        Lang::load(static::getClassName(static::class));
        $this->arrPath = new ArrayPath();
        $this->libExternal = new ExternalLink(StoreRouted::getPath());
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'page.more_lang', false)),
            [],
            boolval(Config::get('Core', 'page.system_prefix', false)),
            boolval(Config::get('Core', 'page.data_separator', false))
        );
        $this->mime = (new Check\Factory())->getLibrary(null);
        $this->sources = (new images_factory(
            (new files_factory(new FilesTranslations()))->getClass($constructParams),
            $this->mime,
            null,
            null,
            new ImagesTranslations()
        ))->getImages($constructParams);
    }

    public function process(): void
    {
    }

    /**
     * @throws FilesException
     * @throws MimeException
     * @throws PathsException
     * @return Output\AOutput
     */
    public function output(): Output\AOutput
    {
        return ($this->params[ISitePart::KEY_LEVEL] == ISitePart::SITE_RESPONSE) ? $this->outResponse() : $this->outLink() ;
    }

    protected function outLink(): Output\AOutput
    {
        $template = new Libs\HeadTemplate();
        $out = new Output\Html();
        return $out->setContent(
            $template->setData(
                $this->libExternal->linkVariant(
                    strval($this->getFromParam('path')),
                    'icon',
                    true
                )
            )->render()
        );
    }

    /**
     * @throws FilesException
     * @throws MimeException
     * @throws PathsException
     * @return Output\AOutput
     */
    protected function outResponse(): Output\AOutput
    {
        $imagePath = $this->innerLink->toUserPath(StoreRouted::getPath()->getPath());
        if ($this->sources->exists($imagePath)) {
            $file = $this->arrPath->setArray($imagePath)->getFileName();
            $content = $this->toString($file, $this->sources->get($imagePath));
        } else {
            $imagePath = realpath(implode(DIRECTORY_SEPARATOR, [
                __DIR__, '..', 'images', 'no_image_available.png'
            ]));
            $content = @file_get_contents($imagePath);
        }

        $out = new Output\Raw();
        if ($content) {
            header('Content-Type: ' . $this->mime->getMime(is_array($imagePath) ? $imagePath : [$imagePath]));
        } else {
            $content = 'Problem with selected image and its backup!';
        }
        return $out->setContent($content);
    }
}
