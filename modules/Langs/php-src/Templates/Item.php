<?php

namespace KWCMS\modules\Langs\Templates;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class Item
 * @package KWCMS\modules\Langs\Templates
 */
class Item extends ATemplate
{
    protected string $moduleName = 'Langs';
    protected string $templateName = 'item';

    protected function fillInputs(): void
    {
        $this->addInput('{LINK}');
        $this->addInput('{ALT}');
        $this->addInput('{IMG}');
        $this->addInput('{WDTH}');
        $this->addInput('{HGHT}');
        $this->addInput('{TITLE}');
        $this->addInput('{SUB}');
    }

    public function setData(string $link, string $title, string $imgPath, string $width, string $height): self
    {
        $this->updateItem('{LINK}', $link);
        $this->updateItem('{ALT}', $title);
        $this->updateItem('{IMG}', $imgPath);
        $this->updateItem('{WDTH}', $width);
        $this->updateItem('{HGHT}', $height);
        $this->updateItem('{TITLE}', $title);
        return $this;
    }
}
