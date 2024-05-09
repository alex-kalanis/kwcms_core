<?php

namespace KWCMS\modules\Rss\Lib;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class HeadTemplate
 * Link in header
 * @package KWCMS\modules\Rss\Lib
 */
class HeadTemplate extends ATemplate
{
    protected string $moduleName = 'Rss';
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
