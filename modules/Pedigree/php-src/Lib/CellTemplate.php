<?php

namespace KWCMS\modules\Pedigree\Lib;


use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;
use kalanis\kw_templates\TemplateException;


/**
 * Class CellTemplate
 * @package KWCMS\modules\Pedigree\Lib
 */
class CellTemplate extends ATemplate
{
    protected string $moduleName = 'Pedigree';
    protected string $templateName = 'cell_norm';

    /* Which styles are available */
    protected static array $styles = [
        'first',
        'norm',
        'ext',
        'no_info',
    ];

    /**
     * @param string $name
     * @throws TemplateException
     * @return $this
     */
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
