<?php

namespace KWCMS\modules\Rss\Lib;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class MainTemplate
 * Main body of RSS feed
 * @package KWCMS\modules\Rss\Lib
 */
class MainTemplate extends ATemplate
{
    protected string $moduleName = 'Rss';
    protected string $templateName = 'main';

    protected function fillInputs(): void
    {
        $this->addInput('{STYLE_PATH}');
        $this->addInput('{TITLE}');
        $this->addInput('{LINK}');
        $this->addInput('{DESC}');
        $this->addInput('{LANG}');
        $this->addInput('{IMAGE}');
        $this->addInput('{ITEMS}');
    }

    public function setData(string $stylePath, string $title, string $link, string $desc, string $lang): self
    {
        $this->updateItem('{STYLE_PATH}', $stylePath);
        $this->updateItem('{TITLE}', $title);
        $this->updateItem('{LINK}', $link);
        $this->updateItem('{DESC}', $desc);
        $this->updateItem('{LANG}', $lang);
        return $this;
    }

    public function addImage(string $imageContent): self
    {
        $this->updateItem('{IMAGE}', $imageContent);
        return $this;
    }

    public function addItems(string $items): self
    {
        $this->updateItem('{ITEMS}', $items);
        return $this;
    }
}
