<?php

namespace KWCMS\modules\Upload\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class ModuleTemplate
 * @package KWCMS\modules\Upload\Lib
 */
class ModuleTemplate extends ATemplate
{
    protected $moduleName = 'Upload';
    protected $templateName = 'module';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{LINK_DASHBOARD}', '#');
        $this->addInput('{LINK_CHDIR}', '#');
        $this->addInput('{TEXT_DASHBOARD}', Lang::get('upload.list_upload'));
        $this->addInput('{TEXT_CHANGE_DIRECTORY}', Lang::get('dashboard.dir_select'));
    }

    public function setData(string $content, string $linkDashboard, string $linkDir): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{LINK_DASHBOARD}', $linkDashboard);
        $this->updateItem('{LINK_CHDIR}', $linkDir);
        return $this;
    }
}
