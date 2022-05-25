<?php

namespace KWCMS\modules\Scripts;


use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class ScriptsTemplate
 * @package KWCMS\modules\Scripts
 */
class ScriptsTemplate extends ATemplate
{
    protected $moduleName = 'Scripts';
    protected $templateName = 'template';

    protected function fillInputs(): void
    {
        $this->addInput('{SCRIPT_PATH}');
    }

    public function setData(string $path): self
    {
        $this->updateItem('{SCRIPT_PATH}', $path);
        return $this;
    }
}
