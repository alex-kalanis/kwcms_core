<?php

namespace KWCMS\modules\Admin\Templates;


use kalanis\kw_auth\Interfaces\IUser;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;


/**
 * Class DashboardTemplate
 * @package KWCMS\modules\Admin\Templates
 */
class DashboardTemplate extends ATemplate
{
    protected $moduleName = 'Admin';
    protected $templateName = 'dashboard';

    protected function fillInputs(): void
    {
        $this->addInput('{MENU}', '');
        $this->addInput('{CONTENT}');
        $this->addInput('{ERRORS}', '');
        $this->addInput('{USERNAME}');
        $this->addInput('{TO_MENU}', Lang::get('system.to_menu'));
        $this->addInput('{TO_UP}', Lang::get('system.to_up'));
        $this->addInput('{TO_DOWN}', Lang::get('system.to_down'));
        $this->addInput('{LOGOUT}', Lang::get('menu.logout'));
        $this->addInput('{CHSETT}', Lang::get('menu.chsett'));
        $this->addInput('{PERSONAL}', Lang::get('menu.personal'));
    }

    public function setData(IUser $user, string $content, string $menu = ''): self
    {
        $this->updateItem('{USERNAME}', $user->getDisplayName());
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{MENU}', $menu);
        return $this;
    }
}
