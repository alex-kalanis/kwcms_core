<?php

namespace KWCMS\modules\Admin\Templates;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class DummyTemplate
 * @package KWCMS\modules\Admin\Templates
 */
class DummyTemplate extends ATemplate
{
    protected string $moduleName = 'Admin';
    protected string $templateName = 'dummy';

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
