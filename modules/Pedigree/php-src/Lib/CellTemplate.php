<?php

namespace KWCMS\modules\Pedigree\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class CellTemplate
 * @package KWCMS\modules\Pedigree\Lib
 */
class CellTemplate extends ATemplate
{
    protected $moduleName = 'Pedigree';
    protected $templateName = 'cell_norm';

    /* Which styles are available */
    protected static $styles = [
        "first",
        "norm",
        "ext",
        "no_info",
    ];

    public function setTemplateName(string $name): self
    {
        if (in_array($name, static::$styles)) {
            $this->templateName = 'cell_' . $name;
            $this->setTemplate($this->loadTemplate());
        }
        return $this;
    }

    protected function fillInputs(): void
    {
        $this->addInput('{SPAN}');
        $this->addInput('{NAME}');
        $this->addInput('{FAMILY}');
        $this->addInput('{PATH}');
        $this->addInput('{MORE}', Lang::get('pedigree.more'));
        $this->addInput('{INFO}');
        $this->addInput('{DESCENDANTS}', Lang::get('pedigree.descendants'));
        $this->addInput('{NO_INFORMATION}', Lang::get('pedigree.no_info'));
    }

    public function setData(string $span, string $name = '', string $family = '', string $link = '', string $info = ''): self
    {
        $this->updateItem('{SPAN}', $span);
        $this->updateItem('{NAME}', $name);
        $this->updateItem('{FAMILY}', $family);
        $this->updateItem('{PATH}', $link);
        $this->updateItem('{INFO}', $info);
        return $this;
    }
}
