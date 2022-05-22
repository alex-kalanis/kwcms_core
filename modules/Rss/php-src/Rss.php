<?php

namespace KWCMS\modules\Rss;


use kalanis\kw_confs\Config;
use kalanis\kw_input\Simplified\ServerAdapter;
use kalanis\kw_mapper\Interfaces\IQueryBuilder;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_paths\Stuff;


/**
 * Class Rss
 * @package KWCMS\modules\Rss
 * Site's short messages - render as Rss feed
 */
class Rss extends AModule
{
    /** @var UserDir|null */
    protected $userDir = null;
    /** @var ExternalLink|null */
    protected $libExternal = null;

    public function __construct()
    {
        Config::load(static::getClassName(static::class));
        $this->userDir = new UserDir(Config::getPath());
        $this->libExternal = new ExternalLink(Config::getPath());
    }

    public function process(): void
    {
    }

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

    public function outResponse(): Output\AOutput
    {
        Config::load('Core', 'page');
        Config::load('Logo');
        $out = new Output\Raw();
        try {
            $tmpl = new Lib\MainTemplate();
            return $out->setContent($tmpl->setData(
                $this->libExternal->linkVariant('rss/rss-style.css', 'styles', true, false),
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
        } catch (MapperException $ex) {
            $error = $ex;
        }
        if (isset($error)) {
            return $out->setContent($error->getMessage());
        }
        return $out;
    }

    /**
     * @return string[]
     * @throws MapperException
     */
    protected function getItems(): array
    {
        $tmplItem = new Lib\ItemTemplate();
        $messages = [];
        try {
            $adapter = new Lib\MessageAdapter(
                $this->userDir->getWebRootDir()
                . Stuff::removeEndingSlash($this->getFromParam('target', '')) . DIRECTORY_SEPARATOR
            );
            $search = new Search($adapter->getRecord());
            $search->offset((int)strval($this->getFromParam('offset', 0)));
            $search->limit((int)strval($this->getFromParam('limit', Config::get('Rss', 'count', 10))));
            $search->orderBy('id', IQueryBuilder::ORDER_DESC);
            $results = $search->getResults();
        } catch (RssException $ex) {
            // no feed in path
            return [];
        }
        foreach ($results as $orm) {
            /** @var Lib\ShortMessage $orm */
            $messages[] = $tmplItem->reset()->setData(
                $this->libExternal->linkVariant(null, ''),
                (string)$orm->title,
                (int)$orm->date,
                (string)$orm->content
            )->render();
        }
        return $messages;
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
