<?php

namespace KWCMS\modules\Router;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;


/**
 * Class RouterTemplate
 * @package KWCMS\modules\Router
 */
class RouterTemplate extends ATemplate
{
    protected $moduleName = 'Router';
    protected $templateName = 'template';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{TITLE}');
        $this->addInput('{ADMINISTRATION}', Lang::get('system.administration'));
    }

    public function setData(string $content, string $title = ''): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{TITLE}', $title ?: Config::get('Core', 'page.page_title'));
        return $this;
    }
}
