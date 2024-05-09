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
use KWCMS\modules\Logo\Libs;


/**
 * Class Logo
 * @package KWCMS\modules\Logo\Controllers
 * Site logo
 */
class Logo extends AModule
{
    protected IMime $mime;
    protected ArrayPath $arrPath;
    protected ExternalLink $extLink;
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
        $this->extLink = new ExternalLink(StoreRouted::getPath());
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'page.more_lang', false)),
            [],
            boolval(Config::get('Core', 'page.system_prefix', false)),
            boolval(Config::get('Core', 'page.data_separator', false))
        );
        $files = (new Factory(new FilesTranslations()))->getClass($constructParams);
        $this->mime = (new Check\Factory())->getLibrary(null);
        $this->processor = $this->getFillLib($files);
    }

    /**
     * @param CompositeAdapter $files
     * @param array<string, string|int> $params
     * @throws ImagesException
     * @return Libs\ImageFill
     */
    protected function getFillLib(CompositeAdapter $files, array $params = []): Libs\ImageFill
    {
        $lang = new ImagesTranslations();
        return new Libs\ImageFill(
            new Libs\ImageProcessor(
                new Graphics\Format\Factory(), $lang
            ),
            (new Graphics\ImageConfig())->setData($params),
            new Sources\Image($files, (new \kalanis\kw_files\Extended\Config())->setData($params)),
            $this->mime,
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
            $mimePath = is_array($logoPath) ? $logoPath : [$logoPath];

            header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', $this->processor->created($mimePath)) );
            header('Content-Type: ' . $this->mime->getMime($mimePath));

            $this->processor->render($mimePath);
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
        $tmpl = new Libs\Template();
        // static on user root
        return $out->setContent($tmpl->setData(
            $this->extLink->linkVariant(['logo.png'], 'File', true, false)
        )->render());
        // dynamic on module
//        return $out->setContent($tmpl->setData(
//            $this->extLink->linkVariant(null, 'Logo', true)
//        )->render());
    }
}
