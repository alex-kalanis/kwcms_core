<?php

namespace KWCMS\modules\Dirlist\Templates;


use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;
use kalanis\kw_paging\Render\SimplifiedPager;


/**
 * Class Main
 * @package KWCMS\modules\Dirlist\Templates
 */
class Main extends ATemplate
{
    protected string $moduleName = 'Dirlist';
    protected string $templateName = 'main';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{PAGER}');
        $this->addInput('{FILES-DIRS}');
        $this->addInput('{FROM}');
        $this->addInput('{SEP}');
        $this->addInput('{TO}');
        $this->addInput('{OF}');
        $this->addInput('{COUNT}');
    }

    public function setData(string $content, SimplifiedPager $pager, $showPagerPositions = false): self
    {
        $this->updateItem('{CONTENT}', $content);
        if ($showPagerPositions) {
            $this->updateItem('{PAGER}', $pager->render(true));
        } else {
            $this->updateItem('{PAGER}', $pager->render(false));
            $this->updateItem('{FILES-DIRS}', Lang::get('dirlist.files_dirs'));
            $this->updateItem('{OF}', Lang::get('dirlist.of'));
            $this->updateItem('{FROM}', $pager->getPager()->getOffset() ? $pager->getPager()->getOffset() + 1 : 1);
            $this->updateItem('{SEP}', '-');
            $this->updateItem('{TO}', strval(min($pager->getPager()->getOffset() + $pager->getPager()->getLimit(), $pager->getPager()->getMaxResults())));
            $this->updateItem('{COUNT}', strval($pager->getPager()->getMaxResults()));
        }
        return $this;
    }
}
