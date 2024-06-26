<?php

namespace KWCMS\modules\Menu\Templates;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class Main
 * @package KWCMS\modules\Menu\Templates
 */
class Main extends ATemplate
{
    protected string $moduleName = 'Menu';
    protected string $templateName = 'menu';

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
