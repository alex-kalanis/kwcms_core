<?php

namespace KWCMS\modules\Watermark\Controllers;


use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\Factory as access_factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Sources;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_mime\MimeException;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Watermark\Libs;


/**
 * Class Watermark
 * @package KWCMS\modules\Watermark\Controllers
 * Watermark over images
 */
class Watermark extends AModule
{
    /** @var MimeType */
    protected $mime = null;
    /** @var string[] */
    protected $imagePath = [];
    /** @var bool */
    protected $repeat = false;
    /** @var ArrayPath */
    protected $arrPath = null;
    /** @var ExternalLink */
    protected $extLink = null;
    /** @var InnerLinks */
    protected $innerLink = null;
    /** @var Libs\ImageFill */
    protected $processor = null;

    public function __construct()
    {
        Config::load(static::getClassName(static::class));
        $this->mime = new MimeType(true);
        $this->extLink = new ExternalLink(Stored::getPath(), StoreRouted::getPath());
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'page.more_lang', false))
        );
        $this->processor = $this->getFillLib(Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot());
    }

    /**
     * @param string $webRootDir
     * @param array<string, string|int> $params
     * @throws FilesException
     * @throws ImagesException
     * @throws PathsException
     * @return Libs\ImageFill
     */
    protected function getFillLib(string $webRootDir, array $params = []): Libs\ImageFill
    {
        $compositeFactory = new access_factory();
        $libProcess = $compositeFactory->getClass($webRootDir);
        return new Libs\ImageFill(
            new Libs\ImageProcessor(
                new Graphics\Format\Factory()
            ),
            (new Graphics\ImageConfig())->setData($params),
            new Sources\Image($libProcess, (new \kalanis\kw_files\Extended\Config())->setData($params))
        );
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
            return $out->setContent('Wrong module run level for watermark image!');
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
    public function createImage()
    {
        $rWatermark = null;
        $rImage = null;

        try {
            $this->arrPath->setArray($this->imagePath);

            // get image itself first
            $imagePath = $this->innerLink->toFullPath($this->imagePath);
            if (!$this->processor->exists($imagePath)) {
                throw new ModuleException('No image with this path!');
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
            header('Content-Type: ' . $this->mime->mimeByPath($this->arrPath->getFileName()));
            header('Content-Disposition: filename="' . $this->arrPath->getFileName(). '"');

            $this->processor->render($imagePath);

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
}
