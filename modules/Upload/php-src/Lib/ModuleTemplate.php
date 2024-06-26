<?php

namespace KWCMS\modules\Upload\Lib;


use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class ModuleTemplate
 * @package KWCMS\modules\Upload\Lib
 */
class ModuleTemplate extends ATemplate
{
    protected string $moduleName = 'Upload';
    protected string $templateName = 'module';

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
