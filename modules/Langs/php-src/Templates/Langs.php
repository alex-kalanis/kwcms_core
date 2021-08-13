<?php

namespace KWCMS\modules\Langs\Templates;


use kalanis\kw_modules\ATemplate;


/**
 * Class Langs
 * @package KWCMS\modules\Langs\Templates
 */
class Langs extends ATemplate
{
    protected $moduleName = 'Langs';
    protected $templateName = 'langs';

    protected function fillInputs(): void
    {
        $this->addInput('{LINK}');
        $this->addInput('{FLAG}');
        $this->addInput('{WDTH}');
        $this->addInput('{HGHT}');
        $this->addInput('{TITLE}');
    }

    public function setData(string $link, string $title, string $imgPath, string $width, string $height): self
    {
        $this->updateItem('{LINK}', $link);
        $this->updateItem('{FLAG}', $imgPath);
        $this->updateItem('{WDTH}', $width);
        $this->updateItem('{HGHT}', $height);
        $this->updateItem('{TITLE}', $title);
        return $this;
    }
}
