<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;


/**
 * Class ModuleTemplate
 * @package KWCMS\modules\Images\Lib
 */
class ModuleTemplate extends ATemplate
{
    protected $moduleName = 'Images';
    protected $templateName = 'module';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{LINK_DASHBOARD}', '#');
        $this->addInput('{LINK_PROPERTIES}', '#');
        $this->addInput('{LINK_MAKE_DIR}', '#');
        $this->addInput('{LINK_UPLOAD}', '#');
        $this->addInput('{LINK_CHDIR}', '#');
        $this->addInput('{TEXT_DASHBOARD}', Lang::get('short.add_record'));
        $this->addInput('{TEXT_PROPERTIES}', Lang::get('short.update_texts'));
        $this->addInput('{TEXT_MAKE_DIR}', Lang::get('short.add_record'));
        $this->addInput('{TEXT_UPLOAD}', Lang::get('short.update_texts'));
        $this->addInput('{TEXT_CHANGE_DIRECTORY}', Lang::get('dashboard.dir_select'));
    }

    public function setData(string $content, string $linkDashboard, string $linkProperties, string $linkMakeDir, string $linkUpload, string $linkDir): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{LINK_DASHBOARD}', $linkDashboard);
        $this->updateItem('{LINK_PROPERTIES}', $linkProperties);
        $this->updateItem('{LINK_MAKE_DIR}', $linkMakeDir);
        $this->updateItem('{LINK_UPLOAD}', $linkUpload);
        $this->updateItem('{LINK_CHDIR}', $linkDir);
        return $this;
    }
}
