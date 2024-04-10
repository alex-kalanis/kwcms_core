<?php

namespace KWCMS\modules\Texts\Lib;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class PreviewTemplate
 * @package KWCMS\modules\Texts\Lib
 */
class PreviewTemplate extends ATemplate
{
    protected string $moduleName = 'Texts';
    protected string $templateName = 'preview';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setData(string $content): self
    {
        $this->updateItem('{CONTENT}', $content);
        return $this;
    }
}
