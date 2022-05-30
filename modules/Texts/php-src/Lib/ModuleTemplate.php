<?php

namespace KWCMS\modules\Texts\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class ModuleTemplate
 * @package KWCMS\modules\Texts\Lib
 */
class ModuleTemplate extends ATemplate
{
    protected $moduleName = 'Texts';
    protected $templateName = 'module';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{LINK_LIST}', '#');
        $this->addInput('{LINK_DIRECTORY}', '#');
        $this->addInput('{LIST_FILES}', Lang::get('texts.edit_file'));
        $this->addInput('{CHANGE_DIRECTORY}', Lang::get('dashboard.dir_select'));
    }

    public function setData(string $content, string $linkUpdate, string $linkDir): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{LINK_LIST}', $linkUpdate);
        $this->updateItem('{LINK_DIRECTORY}', $linkDir);
        return $this;
    }
}
