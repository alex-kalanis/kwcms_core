<?php

namespace KWCMS\modules\Logo\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Sources;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mime\Check;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Logo\Template;
use KWCMS\modules\Logo\Libs;


/**
 * Class Logo
 * @package KWCMS\modules\Logo\Controllers
 * Site logo
 */
class Logo extends AModule
{
    /** @var IMime */
    protected $mime = null;
    /** @var ArrayPath */
    protected $arrPath = null;
    /** @var ExternalLink */
    protected $extLink = null;
    /** @var InnerLinks */
    protected $innerLink = null;
    /** @var Libs\ImageFill */
    protected $processor = null;

    /**
     * @throws ConfException
     * @throws FilesException
     * @throws ImagesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct()
    {
        Config::load(static::getClassName(static::class));
        Lang::load(static::getClassName(static::class));
        $this->extLink = new ExternalLink(Stored::getPath(), StoreRouted::getPath());
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'page.more_lang', false))
        );
        $files = (new Factory())->getClass(
            Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()
        );
        $this->processor = $this->getFillLib($files);
        $this->mime = (new Check\Factory())->getLibrary(null);
    }

    /**
     * @param CompositeAdapter $files
     * @param array<string, string|int> $params
     * @throws ImagesException
     * @return Libs\ImageFill
     */
    protected function getFillLib(CompositeAdapter $files, array $params = []): Libs\ImageFill
    {
        $lang = new Libs\Translations();
        return new Libs\ImageFill(
            new Libs\ImageProcessor(
                new Graphics\Format\Factory(), $lang
            ),
            (new Graphics\ImageConfig())->setData($params),
            new Sources\Image($files, (new \kalanis\kw_files\Extended\Config())->setData($params)),
            $lang
        );
    }

    public function process(): void
    {
    }

    public function output(): Output\AOutput
    {
        return ($this->params[ISitePart::KEY_LEVEL] == ISitePart::SITE_RESPONSE) ? $this->outContent() : $this->outTemplate() ;
    }

    protected function outContent(): Output\AOutput
    {
        $out = new Output\DumpingCallback();
        return $out->setCallback([$this, 'createImage']);
    }

    /**
     * @throws ImagesException
     * @throws MimeException
     * @return string
     */
    public function createImage(): string
    {
        try {
            // get logo image
            $logoPath = $this->innerLink->toUserPath($this->arrPath->setString(Config::get('Logo', 'path'))->getArray());
            if (!$this->processor->exists($logoPath)) {
                $logoPath = $this->innerLink->toModulePath('Logo', ['logo.png']);
            }
            if (!$this->processor->exists($logoPath)) {
                $logoPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo.png';
            }

            $this->processor->process($logoPath);

            header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', $this->processor->created($logoPath)) );
            header('Content-Type: ' . $this->mime->getMime(is_array($logoPath) ? $logoPath : [$logoPath]));

            $this->processor->render($logoPath);
            return '';

        } catch (ImagesException | FilesException | PathsException $ex) {
            return $ex->getMessage();

        } finally {
            $this->processor->close();
        }
    }

    protected function outTemplate(): Output\AOutput
    {
        $out = new Output\Html();
        $tmpl = new Template();
        return $out->setContent($tmpl->setData(
            $this->extLink->linkVariant(null, 'Logo', true)
        )->render());
    }
}
