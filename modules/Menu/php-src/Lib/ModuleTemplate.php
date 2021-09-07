<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;


/**
 * Class ModuleTemplate
 * @package KWCMS\modules\Menu\Lib
 */
class ModuleTemplate extends ATemplate
{
    protected $moduleName = 'Menu';
    protected $templateName = 'module';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{LINK_DASHBOARD}', '#');
        $this->addInput('{LINK_NAMES}', '#');
        $this->addInput('{LINK_POSITIONS}', '#');
        $this->addInput('{LINK_CHDIR}', '#');

        $this->addInput('{TEXT_DASHBOARD}', Lang::get('menu.current'));
        $this->addInput('{TEXT_NAMES}', Lang::get('menu.naming'));
        $this->addInput('{TEXT_POSITIONS}', Lang::get('menu.positions'));
        $this->addInput('{TEXT_CHANGE_DIRECTORY}', Lang::get('dashboard.dir_select'));
    }

    public function setData(
        string $content, string $linkDashboard, string $linkNames, string $linkPositions, string $linkChDir
    ): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{LINK_DASHBOARD}', $linkDashboard);
        $this->updateItem('{LINK_NAMES}', $linkNames);
        $this->updateItem('{LINK_POSITIONS}', $linkPositions);
        $this->updateItem('{LINK_CHDIR}', $linkChDir);
        return $this;
    }
}
