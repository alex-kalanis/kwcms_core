<?php

namespace KWCMS\modules\AdminRouter;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;


/**
 * Class RouterTemplate
 * @package KWCMS\modules\AdminRouter
 */
class RouterTemplate extends ATemplate
{
    protected $moduleName = 'AdminRouter';
    protected $templateName = 'template';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{TITLE}');
        $this->addInput('{BACKGROUND_IMAGE_PATH}');
        $this->addInput('{ADMINISTRATION}', Lang::get('system.administration'));
    }

    public function setData(string $content, string $title = ''): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{BACKGROUND_IMAGE_PATH}', Config::get('Core', 'page.admin_background'));
        $this->updateItem('{TITLE}', $title ?: Config::get('Core', 'page.page_title'));
        return $this;
    }
}
