<?php

namespace KWCMS\modules\Layout;


use kalanis\kw_confs\Config;
use kalanis\kw_modules\ATemplate;


/**
 * Class LayoutTemplate
 * @package KWCMS\modules\Layout
 */
class LayoutTemplate extends ATemplate
{
    protected $moduleName = 'Layout';
    protected $templateName = 'template';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{TITLE}');
        $this->addInput('{LANG}', Config::get('Core', 'page.encoding_lang'));
        $this->addInput('{KEYWORDS}', Config::get('Core', 'page.keywords'));
        $this->addInput('{ABOUT}', Config::get('Core', 'page.about'));
    }

    public function setData(string $content, string $title = ''): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{TITLE}', $title ?: Config::get('Core', 'page.page_title'));
        return $this;
    }
}
