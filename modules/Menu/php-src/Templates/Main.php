<?php

namespace KWCMS\modules\Menu\Templates;


use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class Main
 * @package KWCMS\modules\Menu\Templates
 */
class Main extends ATemplate
{
    protected $moduleName = 'Menu';
    protected $templateName = 'menu';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
    }

    public function setData(string $content): self
    {
        $this->updateItem('{CONTENT}', $content);
        return $this;
    }
}
