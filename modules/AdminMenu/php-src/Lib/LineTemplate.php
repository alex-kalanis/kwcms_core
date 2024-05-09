<?php

namespace KWCMS\modules\AdminMenu\Lib;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class LineTemplate
 * @package KWCMS\modules\AdminMenu\Lib
 */
class LineTemplate extends ATemplate
{
    protected string $moduleName = 'AdminMenu';
    protected string $templateName = 'line';

    protected function fillInputs(): void
    {
        $this->addInput('{HREF}', '#');
        $this->addInput('{ENTRY_TEXT}');
        $this->addInput('{STYLE_TEXT}');
    }

    public function setData(string $link, string $entryName, string $style = ''): self
    {
        $this->updateItem('{HREF}', $link);
        $this->updateItem('{ENTRY_TEXT}', $entryName);
        $this->updateItem('{STYLE_TEXT}', $style);
        return $this;
    }
}
