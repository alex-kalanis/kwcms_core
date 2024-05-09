<?php

namespace KWCMS\modules\Rss\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_input\Simplified\ServerAdapter;
use kalanis\kw_mapper\Interfaces\IQueryBuilder;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\ExternalLink;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Rss\Lib;
use KWCMS\modules\Rss\RssException;


/**
 * Class Rss
 * @package KWCMS\modules\Rss\Controllers
 * Site's short messages - render as Rss feed
 */
class Rss extends AModule
{
    protected ExternalLink $libExternal;
    protected CompositeAdapter $files;
    protected ArrayPath $arrPath;
    protected InnerLinks $innerLink;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        Config::load(static::getClassName(static::class));
        $this->libExternal = new ExternalLink(StoreRouted::getPath());
        $this->arrPath = new ArrayPath();
        $this->files = (new Factory(new FilesTranslations()))->getClass($constructParams);
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            false,
            [],
            boolval(Config::get('Core', 'page.system_prefix', false)),
            boolval(Config::get('Core', 'page.data_separator', false))
        );
    }

    public function process(): void
    {
    }

    /**
     * @throws ConfException
     * @return Output\AOutput
     */
    public function output(): Output\AOutput
    {
        return ($this->params[ISitePart::KEY_LEVEL] == ISitePart::SITE_RESPONSE) ? $this->outResponse() : $this->outLink() ;
    }

    public function outLink(): Output\AOutput
    {
        $template = new Lib\HeadTemplate();
        $out = new Output\Html();
        return $out->setContent($template->setData($this->libExternal->linkVariant('', 'rss', true))->render());
    }

    /**
     * @throws ConfException
     * @return Output\AOutput
     */
    public function outResponse(): Output\AOutput
    {
        Config::load('Core', 'page');
        $out = new Output\Raw();
        try {
            $tmpl = new Lib\MainTemplate();
            return $out->setContent($tmpl->setData(
                $this->libExternal->linkVariant('rss/rss-style.css', 'styles', true),
                Config::get('Core', 'page.site_name'),
                $this->libExternal->linkVariant(),
                Config::get('Core', 'page.page_title'),
                Config::get('Core', 'page.use_lang')
            )->addImage(
                $this->getImage()
            )->addItems(
                implode('', $this->getItems())
            )->render()
            );
        } catch (ConfException | MapperException | FilesException | PathsException | RssException $ex) {
            $error = $ex;
        }
        if (isset($error)) {
            return $out->setContent($error->getMessage());
        }
        return $out;
    }

    /**
     * @throws ConfException
     * @throws FilesException
     * @throws MapperException
     * @throws PathsException
     * @throws RssException
     * @return string[]
     */
    protected function getItems(): array
    {
        $path = $this->pathLookup();
        $tmplItem = new Lib\ItemTemplate();
        $messages = [];
        $adapter = new Lib\MessageAdapter($this->files, $this->innerLink->toFullPath($path));
        $search = new Search($adapter->getRecord());
        $search->offset(intval(strval($this->getFromParam('offset', 0))));
        $search->limit(intval(strval($this->getFromParam('limit', Config::get('Rss', 'count', 10)))));
        $search->orderBy('id', IQueryBuilder::ORDER_DESC);
        $results = $search->getResults();

        foreach ($results as $orm) {
            /** @var Lib\ShortMessage $orm */
            $messages[] = $tmplItem->reset()->setData(
                $this->libExternal->linkVariant($path),
                strval($orm->title),
                intval($orm->date),
                strval($orm->content)
            )->render();
        }
        return $messages;
    }

    /**
     * @return string[]
     */
    protected function pathLookup(): array
    {
        $this->arrPath->setArray(StoreRouted::getPath()->getPath());
        return array_filter(array_merge($this->arrPath->getArrayDirectory(), [Stuff::fileBase($this->arrPath->getFileName())]));
    }

    protected function getImage(): string
    {
        if (!boolval(intval(Config::get('Rss', 'use_image', false)))) {
            return '';
        }
        $serverVars = new ServerAdapter();
        $logoPath = '//' . $serverVars->HTTP_HOST . $this->libExternal->linkVariant(null, 'Logo', true);
        $tmplImage = new Lib\ImageTemplate();
        return $tmplImage->setData( Config::get('Core', 'page.site_name', ''), $logoPath, $logoPath )->render();
    }
}
