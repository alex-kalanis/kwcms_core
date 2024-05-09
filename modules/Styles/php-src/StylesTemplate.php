<?php

namespace KWCMS\modules\Styles;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class StylesTemplate
 * @package KWCMS\modules\Styles
 */
class StylesTemplate extends ATemplate
{
    protected string $moduleName = 'Styles';
    protected string $templateName = 'template';

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
