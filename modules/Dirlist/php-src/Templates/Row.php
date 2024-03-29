<?php

namespace KWCMS\modules\Dirlist\Templates;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class Row
 * @package KWCMS\modules\Dirlist\Templates
 */
class Row extends ATemplate
{
    protected $moduleName = 'Dirlist';
    protected $templateName = 'row';

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
