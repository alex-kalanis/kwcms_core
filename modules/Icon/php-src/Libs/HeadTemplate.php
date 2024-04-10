<?php

namespace KWCMS\modules\Icon\Libs;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class HeadTemplate
 * Link in header
 * @package KWCMS\modules\Icon\Libs
 */
class HeadTemplate extends ATemplate
{
    protected string $moduleName = 'Icon';
    protected string $templateName = 'head';

    protected function fillInputs(): void
    {
        $this->addInput('{LINK}', '#');
    }

    public function setData(string $link): self
    {
        $this->updateItem('{LINK}', $link);
        return $this;
    }
}
