<?php

namespace KWCMS\modules\Chsett\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class ModuleTemplate
 * @package KWCMS\modules\Chsett\Templates
 */
class ModuleTemplate extends ATemplate
{
    protected $moduleName = 'Chsett';
    protected $templateName = 'module';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{LINK_DASHBOARD}', '#');
        $this->addInput('{LINK_ADD_USER}', '#');
        $this->addInput('{LINK_GROUPS}', '#');
        $this->addInput('{LINK_ADD_GROUP}', '#');
        $this->addInput('{TEXT_DASHBOARD}', Lang::get('chsett.module.users'));
        $this->addInput('{TEXT_ADD_USER}', Lang::get('chsett.module.add_user'));
        $this->addInput('{TEXT_GROUPS}', Lang::get('chsett.module.groups'));
        $this->addInput('{TEXT_ADD_GROUP}', Lang::get('chsett.module.add_group'));
    }

    public function setData(string $content, string $linkDashboard, string $linkAddUser, string $linkGroups, string $linkAddGroup): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{LINK_DASHBOARD}', $linkDashboard);
        $this->updateItem('{LINK_ADD_USER}', $linkAddUser);
        $this->updateItem('{LINK_GROUPS}', $linkGroups);
        $this->updateItem('{LINK_ADD_GROUP}', $linkAddGroup);
        return $this;
    }
}
