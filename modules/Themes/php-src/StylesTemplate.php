<?php

namespace KWCMS\modules\Themes;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class StylesTemplate
 * @package KWCMS\modules\Themes
 */
class StylesTemplate extends ATemplate
{
    protected string $moduleName = 'Themes';
    protected string $templateName = 'template';

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
