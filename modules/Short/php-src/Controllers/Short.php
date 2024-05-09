<?php

namespace KWCMS\modules\Short\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_mapper\Interfaces\IQueryBuilder;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Short\Lib;
use KWCMS\modules\Short\ShortException;


/**
 * Class Short
 * @package KWCMS\modules\Short\Controllers
 * Site's short messages - render on page
 */
class Short extends AModule
{
    /** @var Search */
    protected ?Search $search = null;
    /** @var MapperException|null */
    protected $error = null;
    protected CompositeAdapter $files;
    protected InnerLinks $innerLink;
    protected ArrayPath $arrPath;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        Config::load(static::getClassName(static::class));
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            false,
            [],
            boolval(Config::get('Core', 'page.system_prefix', false)),
            boolval(Config::get('Core', 'page.data_separator', false))
        );
        $this->files = (new Factory(new FilesTranslations()))->getClass($constructParams);
    }

    public function process(): void
    {
        try {
            $adapter = new Lib\MessageAdapter($this->files, $this->innerLink->toFullPath($this->pathLookup()));
            $this->search = new Search($adapter->getRecord());
        } catch (ConfException | FilesException | MapperException | PathsException | ShortException $ex) {
            $this->error = $ex;
        }
    }

    /**
     * @throws PathsException
     * @return string[]
     */
    protected function pathLookup(): array
    {
        if (empty($this->params['target'])) {
            $this->arrPath->setArray(StoreRouted::getPath()->getPath());
            return array_merge($this->arrPath->getArrayDirectory(), [Stuff::fileBase($this->arrPath->getFileName())]);
        }
        $preset = strval($this->params['target']);
        return (IPaths::SPLITTER_SLASH != $preset[0])
            ? array_merge(StoreRouted::getPath()->getPath(), Stuff::linkToArray(Stuff::fileBase($preset))) // add current path to wanted content
            : Stuff::linkToArray(Stuff::fileBase(mb_substr($preset, 1))); // just remove that slash and return path
    }

    public function output(): Output\AOutput
    {
        $tmpl = new Lib\MessageTemplate();
        $messages = [];
        if ($this->search) {
            try {
                $this->search->offset(intval($this->getFromParam('offset', 0)));
                $this->search->limit(intval($this->getFromParam('limit', Config::get('Short', 'count', 100))));
                $this->search->orderBy('id', IQueryBuilder::ORDER_DESC);
                $results = $this->search->getResults();
                foreach ($results as $orm) {
                    /** @var Lib\ShortMessage $orm */
                    $messages[] = $tmpl->reset()->setData(
                        intval($orm->date),
                        strval($orm->title),
                        strval($orm->content)
                    )->render();
                }
            } catch (MapperException $ex) {
                $this->error = $ex;
            }
        }

        $out = new Output\Html();
        if ($this->error) {
            return $out->setContent($this->error->getMessage());
        } else {
            return $out->setContent(implode('', $messages));
        }
    }
}
