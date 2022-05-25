<?php

namespace KWCMS\modules\Admin\Templates;


use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class DummyTemplate
 * @package KWCMS\modules\Admin\Templates
 */
class DummyTemplate extends ATemplate
{
    protected $moduleName = 'Admin';
    protected $templateName = 'dummy';

    protected function fillInputs(): void
    {
        $this->addInput('{MESSAGE}', '');
    }

    public function setData(string $content): self
    {
        $this->updateItem('{MESSAGE}', $content);
        return $this;
    }
}
