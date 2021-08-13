<?php

namespace KWCMS\modules\Styles;


use kalanis\kw_modules\ATemplate;


/**
 * Class StylesTemplate
 * @package KWCMS\modules\Styles
 */
class StylesTemplate extends ATemplate
{
    protected $moduleName = 'Styles';
    protected $templateName = 'template';

    protected function fillInputs(): void
    {
        $this->addInput('{STYLE_PATH}');
    }

    public function setData(string $path): self
    {
        $this->updateItem('{STYLE_PATH}', $path);
        return $this;
    }
}
