<?php

namespace KWCMS\modules\Dirlist\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;
use kalanis\kw_paging\Render\SimplifiedPager;


/**
 * Class Main
 * @package KWCMS\modules\Dirlist\Templates
 */
class Main extends ATemplate
{
    protected $moduleName = 'Dirlist';
    protected $templateName = 'main';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{PAGER}');
        $this->addInput('{FILES-DIRS}', Lang::get('dirlist.files_dirs'));
        $this->addInput('{FROM}');
        $this->addInput('{TO}');
        $this->addInput('{OF}', Lang::get('dirlist.of'));
        $this->addInput('{COUNT}');
    }

    public function setData(string $content, SimplifiedPager $pager): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{PAGER}', $pager->render());
        $this->updateItem('{FROM}', $pager->getPager()->getOffset() ? $pager->getPager()->getOffset() + 1 : 0);
        $this->updateItem('{TO}', strval(min($pager->getPager()->getOffset() + $pager->getPager()->getLimit(), $pager->getPager()->getMaxResults())));
        $this->updateItem('{COUNT}', strval($pager->getPager()->getMaxResults()));
        return $this;
    }
}
