<?php

namespace KWCMS\modules\MediaRss\Lib;


use kalanis\kw_modules\ATemplate;


/**
 * Class ItemTemplate
 * @package KWCMS\modules\MediaRss\Lib
 */
class ItemTemplate extends ATemplate
{
    protected $moduleName = 'MediaRss';
    protected $templateName = 'item';

    protected function fillInputs(): void
    {
        $this->addInput('{LINK}');
        $this->addInput('{TITLE}');
        $this->addInput('{DESC}');
        $this->addInput('{THUMB}');
        $this->addInput('{PATH}');
    }

    public function setData(string $link, string $title, string $message, string $thumb, string $path): self
    {
        $this->updateItem('{LINK}', $link);
        $this->updateItem('{TITLE}', $title);
        $this->updateItem('{DESC}', $message);
        $this->updateItem('{THUMB}', $thumb);
        $this->updateItem('{PATH}', $path);
        return $this;
    }
}
