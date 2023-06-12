<?php

namespace KWCMS\modules\Themes;


use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class StylesTemplate
 * @package KWCMS\modules\Themes
 */
class StylesTemplate extends ATemplate
{
    protected $moduleName = 'Themes';
    protected $templateName = 'template';

    protected function fillInputs(): void
    {
        $this->addInput('{STYLE_TITLE}');
        $this->addInput('{STYLE_PATH}');
        $this->addInput('{STYLE_ID}');
    }

    public function setData(string $path, string $title, string $id): self
    {
        $this->updateItem('{STYLE_TITLE}', $title);
        $this->updateItem('{STYLE_PATH}', $path);
        $this->updateItem('{STYLE_ID}', $id);
        return $this;
    }
}
