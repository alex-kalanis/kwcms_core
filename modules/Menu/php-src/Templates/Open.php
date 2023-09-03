<?php

namespace KWCMS\modules\Menu\Templates;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class Open
 * @package KWCMS\modules\Menu\Templates
 */
class Open extends ATemplate
{
    protected $moduleName = 'Menu';
    protected $templateName = 'open';

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
