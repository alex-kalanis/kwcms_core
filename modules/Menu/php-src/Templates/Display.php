<?php

namespace KWCMS\modules\Menu\Templates;


use kalanis\kw_modules\ATemplate;


/**
 * Class Display
 * @package KWCMS\modules\Menu\Templates
 */
class Display extends ATemplate
{
    protected $moduleName = 'Menu';
    protected $templateName = 'item';

    /* Which styles are available */
    protected static $styles = [
        "free",
        "head",
        "item",
    ];

    public function setTemplateName(string $name): self
    {
        if (in_array($name, static::$styles)) {
            $this->templateName = $name;
            $this->setTemplate($this->loadTemplate());
        }
        return $this;
    }

    protected function fillInputs(): void
    {
        $this->addInput('{ALT}');
        $this->addInput('{LINK}');
        $this->addInput('{NAME}');
        $this->addInput('{SUB}');
    }

    public function setData(string $name, string $title = '', string $link = '', string $sub = ''): self
    {
        $this->updateItem('{NAME}', $name);
        $this->updateItem('{ALT}', $title);
        $this->updateItem('{LINK}', $link);
        $this->updateItem('{SUB}', $sub);
        return $this;
    }
}
