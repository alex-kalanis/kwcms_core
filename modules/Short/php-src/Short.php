<?php

namespace KWCMS\modules\Short;


use kalanis\kw_confs\Config;
use kalanis\kw_mapper\Interfaces\IQueryBuilder;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Output;
use kalanis\kw_short\ShortMessage;


/**
 * Class Short
 * @package KWCMS\modules\Short
 * Site's short messages - render on page
 */
class Short extends AModule
{
    /** @var Search|null */
    protected $search = null;
    /** @var MapperException|null */
    protected $error = null;

    public function process(): void
    {
        Config::load('Short');
        try {
            $this->search = new Search(new ShortMessage());
        } catch (MapperException $ex) {
            $this->error = $ex;
        }
    }

    public function output(): Output\AOutput
    {
        $tmpl = new MessageTemplate();
        $messages = [];
        try {
            $this->search->offset((int)$this->getFromParam('offset', 0));
            $this->search->limit((int)$this->getFromParam('limit', Config::get('Short', 'count', 100)));
            $this->search->orderBy('id', IQueryBuilder::ORDER_DESC);
            $results = $this->search->getResults();
            foreach ($results as $orm) {
                /** @var ShortMessage $orm */
                $messages[] = $tmpl->reset()->setData((int)$orm->date, (string)$orm->title, (string)$orm->content)->render();
            }
        } catch (MapperException $ex) {
            $this->error = $ex;
        }

        $out = new Output\Html();
        if ($this->error) {
            return $out->setContent($ex->getMessage());
        } else {
            return $out->setContent(implode('', $messages));
        }
    }
}
