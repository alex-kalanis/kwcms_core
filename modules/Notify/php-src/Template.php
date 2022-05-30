<?php

namespace KWCMS\modules\Notify;


use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class Template
 * @package KWCMS\modules\Notify
 */
class Template extends ATemplate
{
    protected $moduleName = 'Notify';
    protected $templateName = 'template';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT_CLASS}');
        $this->addInput('{BOLD_TEXT}');
        $this->addInput('{CONTENT_TEXT}');
    }

    public function setData(string $type, string $message, string $class = ''): self
    {
        $this->updateItem('{BOLD_TEXT}', $type);
        $this->updateItem('{CONTENT_TEXT}', $message);
        $this->updateItem('{CONTENT_CLASS}', $class);
        return $this;
    }
}
