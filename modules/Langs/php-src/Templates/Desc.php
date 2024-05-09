<?php

namespace KWCMS\modules\Langs\Templates;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class Desc
 * @package KWCMS\modules\Langs\Templates
 */
class Desc extends ATemplate
{
    protected string $moduleName = 'Langs';
    protected string $templateName = 'desc';

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
