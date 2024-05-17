<?php

namespace KWCMS\modules\Image\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\Factory as files_factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_images\Access\Factory as images_factory;
use kalanis\kw_images\Content\Images;
use kalanis\kw_images\ImagesException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mime\Check;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\ExternalLink;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Core\Libs\ImagesTranslations;
use KWCMS\modules\Image\Libs;
use KWCMS\modules\Layout\Controllers\Layout;


/**
 * Class Image
 * @package KWCMS\modules\Image\Controllers
 * Users images - controller
 */
class Image extends AModule
{
    use TToString;

    protected IMime $mime;
    protected ArrayPath $arrPath;
    protected ExternalLink $extLink;
    protected InnerLinks $innerLink;
    protected Images $sources;
    /** @var mixed */
    protected $constructParams = [];

    /***
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws ImagesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        Config::load(static::getClassName(static::class));
        Lang::load(static::getClassName(static::class));
        $this->constructParams = $constructParams;
        $this->extLink = new ExternalLink(StoreRouted::getPath());
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'page.more_lang', false)),
            array_filter(Stuff::linkToArray(Config::get('Core', 'page.image_prefix', ''))),
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
     * @throws ConfException
     * @throws FilesException
     * @throws MimeException
     * @throws ModuleException
     * @throws PathsException
     * @return Output\AOutput
     */
    public function output(): Output\AOutput
    {
        $path = StoreRouted::getPath()->getPath();
        return (StoreRouted::getPath()->isSingle())
            ? $this->outImage($path)
            : (($this->params[ISitePart::KEY_LEVEL] == ISitePart::SITE_RESPONSE)
                ? $this->outLayout($this->outTemplate($path))
                : $this->outTemplate($path)
            ) ;
    }

    /**
     * @param string[] $path
     * @throws MimeException
     * @throws PathsException
     * @throws FilesException
     * @return Output\AOutput
     */
    protected function outImage(array $path): Output\AOutput
    {
        $out = new Output\Raw();
        $name = $this->arrPath->setArray($path)->getFileName();
        $imagePath = $this->innerLink->toFullPath($path);
        if ($this->sources->exists($imagePath)) {
            $content = $this->toString($name, $this->sources->get($imagePath));
        } else {
            $path = realpath(implode(DIRECTORY_SEPARATOR, [
                __DIR__, '..', '..', 'images', 'no_image_available.png'
            ]));
            $content = @file_get_contents($path);
        }
        if ($content) {
            header('Content-Type: ' . $this->mime->getMime($imagePath));
        } else {
            $content = 'Problem with selected image and its backup!';
        }
        return $out->setContent($content);
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return Output\AOutput
     */
    protected function outTemplate(array $path): Output\AOutput
    {
        $out = new Output\Html();
        $tmpl = new Libs\Template();
        return $out->setContent($tmpl->setData(
            $this->imagePath($path),
            $this->descriptionFile($path),
            $this->imageCreated($path)
        )->render());
    }

    /**
     * @param Output\AOutput $output
     * @throws ConfException
     * @throws ModuleException
     * @return Output\AOutput
     */
    protected function outLayout(Output\AOutput $output): Output\AOutput
    {
        $out = new Layout(...$this->constructParams);
        $out->init($this->inputs, $this->params);
        return $out->wrapped($output, false);
    }

    /**
     * @param string[] $path
     * @throws PathsException
     * @return string
     */
    protected function imagePath(array $path): string
    {
        $linkPath = $this->arrPath->setArray($path)->getString();
        $hasWatermark = (bool) Config::get('Image', 'watermark', false);
        $canWatermark = (array) Config::get('Image', 'accept_watermark', []);
        return $hasWatermark && in_array(strtolower(Stuff::fileExt($this->arrPath->getFileName())), $canWatermark)
            ? $this->extLink->linkVariant($linkPath, 'watermark', true)
            : $this->extLink->linkVariant($linkPath, 'image', true) ;
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    protected function imageCreated(array $path): string
    {
        $dateFormat = Config::get('Image', 'date_format', 'd.m.Y\ \@\ H:i:s');
        return strval($this->sources->created($this->innerLink->toFullPath($path), $dateFormat));
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    protected function descriptionFile(array $path): string
    {
        return strval($this->sources->getDescription($this->innerLink->toFullPath($path)));
    }
}
