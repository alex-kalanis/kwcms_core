<?php

namespace KWCMS\modules\Watermark\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory as access_factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_images\Configs;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Sources;
use kalanis\kw_input\Interfaces\IEntry;
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
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Core\Libs\ImagesTranslations;
use KWCMS\modules\Watermark\Libs;


/**
 * Class Watermark
 * @package KWCMS\modules\Watermark\Controllers
 * Watermark over images
 */
class Watermark extends AModule
{
    protected IMime $mime;
    /** @var string[] */
    protected array $imagePath = [];
    protected bool $repeat = false;
    protected ArrayPath $arrPath;
    protected InnerLinks $innerLink;
    protected Libs\ImageFill $processor;

    /**
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
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'page.more_lang', false)),
            [],
            boolval(Config::get('Core', 'page.system_prefix', false)),
            boolval(Config::get('Core', 'page.data_separator', false))
        );
        $libProcess = (new access_factory(new FilesTranslations()))->getClass($constructParams);
        $this->mime = $this->getMimeLib($libProcess);
        $this->processor = $this->getFillLib($libProcess, $this->mime);
    }

    /**
     * @param CompositeAdapter $files
     * @param IMime $mime
     * @param array<string, string|int> $params
     * @throws ImagesException
     * @return Libs\ImageFill
     */
    protected function getFillLib(CompositeAdapter $files, IMime $mime, array $params = []): Libs\ImageFill
    {
        $lang = new ImagesTranslations();
        return new Libs\ImageFill(
            new Libs\ImageProcessor(
                new Graphics\Format\Factory(), $lang
            ),
            (new Configs\ImageConfig())->setData($params),
            new Sources\Image($files, (new \kalanis\kw_files\Extended\Config())->setData($params)),
            $mime,
            $lang
        );
    }

    protected function getMimeLib(CompositeAdapter $files): IMime
    {
        return (new Check\Factory())->getLibrary($files);
    }

    public function process(): void
    {
        $repeat = $this->inputs->getInArray('repeat', [
            IEntry::SOURCE_CLI, IEntry::SOURCE_POST, IEntry::SOURCE_GET
        ]);
        $this->repeat = !empty($repeat);
        $this->imagePath = StoreRouted::getPath()->getPath();
    }

    public function output(): Output\AOutput
    {
        if ($this->params[ISitePart::KEY_LEVEL] != ISitePart::SITE_RESPONSE) {
            $out = new Output\Raw();
            return $out->setContent(Lang::get('watermark.module.wrong_level'));
        }
        $out = new Output\DumpingCallback();
        return $out->setCallback([$this, 'createImage']);
    }

    /**
     * Create image with watermark
     * @throws FilesException
     * @throws ImagesException
     * @throws PathsException
     * @throws MimeException
     * @return string
     */
    public function createImage(): string
    {
        try {
            $this->arrPath->setArray($this->imagePath);

            // get image itself first
            $imagePath = $this->innerLink->toFullPath($this->imagePath);
            if (!$this->processor->exists($imagePath)) {
                throw new ModuleException(Lang::get('watermark.module.no_image'));
            }

            // then get watermark
            $watermarkPath = $this->innerLink->toUserPath(['watermark.png']);
            if (!$this->processor->exists($watermarkPath)) {
                $watermarkPath = $this->innerLink->toModulePath('Watermark', ['watermark.png']);
            }
            if (!$this->processor->exists($watermarkPath)) {
                $watermarkPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'watermark.png';
            }

            $this->processor->process($imagePath, $watermarkPath, $this->repeat);

            header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', $this->processor->created($imagePath)) );
            header('Content-Type: ' . $this->mime->getMime($imagePath));
            header('Content-Disposition: filename="' . $this->arrPath->getFileName(). '"');

            $this->processor->render($imagePath);

            return '';
        } catch (ModuleException $ex) {
            return $ex->getMessage();

        } finally {

            $this->processor->close();
        }
    }
}
