<?php

namespace KWCMS\modules\MediaRss\Lib;


use kalanis\kw_modules\ATemplate;


/**
 * Class MainTemplate
 * Main body of MediaRss feed
 * @package KWCMS\modules\MediaRss\Lib
 */
class MainTemplate extends ATemplate
{
    protected $moduleName = 'MediaRss';
    protected $templateName = 'main';

    protected function fillInputs(): void
    {
        $this->addInput('{STYLE_PATH}');
        $this->addInput('{TITLE}');
        $this->addInput('{LINK}');
        $this->addInput('{DESC}');
        $this->addInput('{ITEMS}');
    }

    public function setData(string $stylePath, string $title, string $link, string $desc): self
    {
        $this->updateItem('{STYLE_PATH}', $stylePath);
        $this->updateItem('{TITLE}', $title);
        $this->updateItem('{LINK}', $link);
        $this->updateItem('{DESC}', $desc);
        return $this;
    }

    public function addItems(string $items): self
    {
        $this->updateItem('{ITEMS}', $items);
        return $this;
    }
}
