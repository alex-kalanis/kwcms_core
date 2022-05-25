<?php

namespace KWCMS\modules\Rss\Lib;


use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class ImageTemplate
 * @package KWCMS\modules\Rss\Lib
 */
class ImageTemplate extends ATemplate
{
    protected $moduleName = 'Rss';
    protected $templateName = 'image';

    protected function fillInputs(): void
    {
        $this->addInput('{TITLE}');
        $this->addInput('{LINK}');
        $this->addInput('{URL}');
    }

    public function setData(string $title, string $link, string $url): self
    {
        $this->updateItem('{TITLE}', $title);
        $this->updateItem('{LINK}', $link);
        $this->updateItem('{URL}', $url);
        return $this;
    }
}
