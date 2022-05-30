<?php

namespace KWCMS\modules\Rss\Lib;


use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class ItemTemplate
 * @package KWCMS\modules\Rss\Lib
 */
class ItemTemplate extends ATemplate
{
    protected $moduleName = 'Rss';
    protected $templateName = 'item';

    protected function fillInputs(): void
    {
        $this->addInput('{LINK}');
        $this->addInput('{TITLE}');
        $this->addInput('{DATE}');
        $this->addInput('{DESC}');
    }

    public function setData(string $link, string $title, int $date, string $message): self
    {
        $this->updateItem('{LINK}', $link);
        $this->updateItem('{TITLE}', $title);
        $this->updateItem('{DATE}', date('d M Y h:i:s \G\M\T', $date));
        $this->updateItem('{DESC}', $message);
        return $this;
    }
}
