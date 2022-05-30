<?php

namespace KWCMS\modules\Personal\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class ModuleTemplate
 * @package KWCMS\modules\Personal\Templates
 */
class ModuleTemplate extends ATemplate
{
    protected $moduleName = 'Personal';
    protected $templateName = 'module';

    protected function fillInputs(): void
    {
        $this->addInput('{CONTENT}');
        $this->addInput('{LINK_PROPS}', '#');
        $this->addInput('{LINK_PASS}', '#');
        $this->addInput('{TEXT_PROPS}', Lang::get('personal.properties'));
        $this->addInput('{TEXT_PASS}', Lang::get('personal.passwords'));
    }

    public function setData(string $content, string $linkDetails, string $linkPass): self
    {
        $this->updateItem('{CONTENT}', $content);
        $this->updateItem('{LINK_PROPS}', $linkDetails);
        $this->updateItem('{LINK_PASS}', $linkPass);
        return $this;
    }
}
