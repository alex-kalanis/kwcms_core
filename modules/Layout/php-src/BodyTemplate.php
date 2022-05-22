<?php

namespace KWCMS\modules\Layout;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;


/**
 * Class BodyTemplate
 * @package KWCMS\modules\Layout
 */
class BodyTemplate extends ATemplate
{
    protected $moduleName = 'Layout';
    protected $templateName = 'body';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{SITE_NAME}', Config::get('Core', 'page.site_name'));
        $this->addInput('{TO_MENU}', Lang::get('to_menu'));
        $this->addInput('{NOSCRIPT}', Lang::get('noscript'));
    }

    public function setData(string $content): self
    {
        $this->updateItem('{CONTENT}', $content);
        return $this;
    }
}
