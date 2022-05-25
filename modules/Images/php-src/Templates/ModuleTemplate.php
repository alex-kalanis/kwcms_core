<?php

namespace KWCMS\modules\Images\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class ModuleTemplate
 * @package KWCMS\modules\Images\Templates
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
        $this->addInput('{TEXT_DASHBOARD}', Lang::get('images.list_dir'));
        $this->addInput('{TEXT_PROPERTIES}', Lang::get('images.dir_props'));
        $this->addInput('{TEXT_MAKE_DIR}', Lang::get('images.create_dir'));
        $this->addInput('{TEXT_UPLOAD}', Lang::get('images.upload_image'));
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
