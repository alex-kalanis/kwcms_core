<?php

namespace KWCMS\modules\Langs\Templates;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class Menu
 * @package KWCMS\modules\Langs\Templates
 */
class Menu extends ATemplate
{
    protected $moduleName = 'Langs';
    protected $templateName = 'menu';

    protected function fillInputs(): void
    {
        $this->addInput('{CONT}');
    }

    public function setData(string $content): self
    {
        $this->updateItem('{CONT}', $content);
        return $this;
    }
}
