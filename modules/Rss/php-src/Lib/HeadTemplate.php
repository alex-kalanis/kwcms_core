<?php

namespace KWCMS\modules\Rss\Lib;


use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class HeadTemplate
 * Link in header
 * @package KWCMS\modules\Rss\Lib
 */
class HeadTemplate extends ATemplate
{
    protected $moduleName = 'Rss';
    protected $templateName = 'head';

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
