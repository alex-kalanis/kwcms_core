<?php

namespace KWCMS\modules\SinglePage;


use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class PageTemplate
 * @package KWCMS\modules\SinglePage
 */
class PageTemplate extends ATemplate
{
    protected string $moduleName = 'SinglePage';
    protected string $templateName = 'template';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{TITLE}');
        $this->addInput('{SITE_NAME}', Config::get('Core', 'page.site_name'));
        $this->addInput('{LANG}', Config::get('Core', 'page.encoding_lang'));
        $this->addInput('{KEYWORDS}', Config::get('Core', 'page.keywords'));
        $this->addInput('{ABOUT}', Config::get('Core', 'page.about'));
        $this->addInput('{TO_MENU}', Lang::get('to_menu'));
        $this->addInput('{NOSCRIPT}', Lang::get('noscript'));
    }

    public function setData(string $content, string $title = ''): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{TITLE}', $title ?: Config::get('Core', 'page.page_title'));
        return $this;
    }
}
