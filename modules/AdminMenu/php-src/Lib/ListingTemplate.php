<?php

namespace KWCMS\modules\AdminMenu\Lib;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class ListingTemplate
 * @package KWCMS\modules\AdminMenu\Lib
 */
class ListingTemplate extends ATemplate
{
    protected string $moduleName = 'AdminMenu';
    protected string $templateName = 'listing';

    protected function fillInputs(): void
    {
        $this->addInput('{LINK_TOP}', '#');
        $this->addInput('{TOP_CLASS}');
        $this->addInput('{PARAMS}');
        $this->addInput('{TOP_NAME}');
        $this->addInput('{SUB_ENTRIES}');
    }

    public function setData(string $topLink, string $topClass, string $topName, string $params = ''): self
    {
        $this->updateItem('{LINK_TOP}', $topLink);
        $this->updateItem('{TOP_CLASS}', $topClass);
        $this->updateItem('{TOP_NAME}', $topName);
        $this->updateItem('{PARAMS}', $params);
        return $this;
    }

    public function addSubEntry(string $entry): self
    {
        $this->updateItem('{SUB_ENTRIES}', $this->getItem('{SUB_ENTRIES}')->getValue() . $entry);
        return $this;
    }
}
