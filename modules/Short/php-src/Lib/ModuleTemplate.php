<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;


/**
 * Class ModuleTemplate
 * @package KWCMS\modules\Short\Lib
 */
class ModuleTemplate extends ATemplate
{
    protected $moduleName = 'Short';
    protected $templateName = 'module';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{LINK_ADD}', '#');
        $this->addInput('{LINK_UPDATE}', '#');
        $this->addInput('{LINK_DIRECTORY}', '#');
        $this->addInput('{ADD_RECORD}', Lang::get('short.add_record'));
        $this->addInput('{UPDATE_TEXTS}', Lang::get('short.update_texts'));
        $this->addInput('{CHANGE_DIRECTORY}', Lang::get('dashboard.dir_select'));
    }

    public function setData(string $content, string $linkAdd, string $linkUpdate, string $linkDir): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{LINK_ADD}', $linkAdd);
        $this->updateItem('{LINK_UPDATE}', $linkUpdate);
        $this->updateItem('{LINK_DIRECTORY}', $linkDir);
        return $this;
    }
}
