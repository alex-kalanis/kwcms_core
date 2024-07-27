<?php

namespace KWCMS\modules\Image\AdminControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\Factory as files_factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_images\Access\Factory as images_factory;
use kalanis\kw_images\Content\Images;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Simplified\SessionAdapter;
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
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Admin\Shared\ChDirTranslations;
use KWCMS\modules\Core\Libs\AAuthModule;
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
class Image extends AAuthModule
{
    use TToString;
    use TWhereDir;

    protected IMime $mime;
    protected ArrayPath $arrPath;
    protected ExternalLink $extLink;
    protected UserDir $userDir;
    protected Images $sources;
    /** @var string[] */
    protected array $userPath = [];
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
        $this->userDir = new UserDir(new ChDirTranslations());
        $this->extLink = new ExternalLink(StoreRouted::getPath());
        $this->arrPath = new ArrayPath();
        $this->mime = (new Check\Factory())->getLibrary(null);
        $this->sources = (new images_factory(
            (new files_factory(new FilesTranslations()))->getClass($constructParams),
            $this->mime,
            null,
            null,
            new ImagesTranslations()
        ))->getImages($constructParams);

    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new SessionAdapter(), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());
        $this->userPath = array_filter(array_values($this->userDir->process()->getFullPath()->getArray()));
    }

    /**
     * @throws ConfException
     * @throws FilesException
     * @throws MimeException
     * @throws ModuleException
     * @throws PathsException
     * @return Output\AOutput
     */
    public function result(): Output\AOutput
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
        $content = $this->getContentWithMime($path);
        if (empty($content)) {
            $content = 'Problem with selected image and its backup!';
        }
        return $out->setContent($content);
    }

    /**
     * @param string[] $path
     * @return string
     * @throws FilesException
     * @throws MimeException
     * @throws PathsException
     */
    public function getContentWithMime(array $path): string
    {
        list($content, $imagePath) = $this->getContentData($path);
        if ($imagePath) {
            header('Content-Type: ' . $this->mime->getMime($imagePath));
        }
        return $content;
    }

    /**
     * @param string[] $path
     * @return array{
     *     string,
     *     string[]|null
     * }
     * @throws FilesException
     * @throws PathsException
     */
    protected function getContentData(array $path): array
    {
        $name = $this->arrPath->setArray($path)->getFileName();
        $imagePath = array_merge($this->userPath, $path);
        if ($this->sources->exists($imagePath)) {
            return [$this->toString($name, $this->sources->get($imagePath)), $imagePath];
        }

        $path = realpath(implode(DIRECTORY_SEPARATOR, [
            __DIR__, '..', '..', 'images', 'no_image_available.png'
        ]));
        return [@file_get_contents($path), ['no_image_available.png']];
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
        $hasWatermark = (bool) Config::get('Image', 'watermark', false);
        $canWatermark = (array) Config::get('Image', 'accept_watermark', []);
        return $hasWatermark && in_array(strtolower(Stuff::fileExt($this->arrPath->getFileName())), $canWatermark)
            ? $this->extLink->linkVariant($path, 'watermark', true)
            : $this->extLink->linkVariant($path, 'image', true) ;
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
        return strval($this->sources->created(array_merge($this->userPath, $path), $dateFormat));
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    protected function descriptionFile(array $path): string
    {
        return $this->sources->getDescription(array_merge($this->userPath, $path));
    }
}
