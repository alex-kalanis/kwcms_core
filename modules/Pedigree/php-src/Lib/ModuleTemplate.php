<?php

namespace KWCMS\modules\Pedigree\Lib;


use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class ModuleTemplate
 * @package KWCMS\modules\Pedigree\Lib
 */
class ModuleTemplate extends ATemplate
{
    protected $moduleName = 'Pedigree';
    protected $templateName = 'module';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{LINK_DASHBOARD}', '#');
        $this->addInput('{LINK_ADD}', '#');
        $this->addInput('{SHOW_TABLE}', Lang::get('pedigree.show_table'));
        $this->addInput('{ADD_RECORD}', Lang::get('pedigree.add_record'));
    }

    public function setData(string $content, string $linkDashboard, string $linkAdd): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{LINK_DASHBOARD}', $linkDashboard);
        $this->updateItem('{LINK_ADD}', $linkAdd);
        return $this;
    }
}
