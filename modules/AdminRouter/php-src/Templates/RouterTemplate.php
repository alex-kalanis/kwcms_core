<?php

namespace KWCMS\modules\AdminRouter\Templates;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class RouterTemplate
 * @package KWCMS\modules\AdminRouter\Templates
 */
class RouterTemplate extends ATemplate
{
    protected string $moduleName = 'AdminRouter';
    protected string $templateName = 'template';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{TITLE}');
        $this->addInput('{BACKGROUND_IMAGE_PATH}');
        $this->addInput('{ADMINISTRATION}', Lang::get('system.administration'));
        $this->addInput('{TO_MENU}', Lang::get('system.to_menu'));
        $this->addInput('{TO_UP}', Lang::get('system.to_up'));
        $this->addInput('{TO_DOWN}', Lang::get('system.to_down'));
        $this->addInput('{LOGOUT}', Lang::get('menu.logout'));
        $this->addInput('{CHSETT}', Lang::get('menu.chsett'));
        $this->addInput('{PERSONAL}', Lang::get('menu.personal'));
        $this->addInput('{TOP_ROW_TEMPLATE}');
        $this->addInput('{FOOT_ROW_TEMPLATE}');
    }

    public function setData(string $content, string $title = ''): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{BACKGROUND_IMAGE_PATH}', Config::get('Core', 'page.admin_background'));
        $this->updateItem('{TITLE}', $title ?: Config::get('Core', 'page.page_title'));
        $this->updateItem('{CONTENT}', $content);
        return $this;
    }

    public function setTopRow(string $content): self
    {
        $this->updateItem('{TOP_ROW_TEMPLATE}', $content);
        return $this;
    }

    public function setFootRow(string $content): self
    {
        $this->updateItem('{FOOT_ROW_TEMPLATE}', $content);
        return $this;
    }
}
