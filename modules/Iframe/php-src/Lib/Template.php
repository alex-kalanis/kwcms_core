<?php

namespace KWCMS\modules\Iframe\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;


/**
 * Class Template
 * @package KWCMS\modules\Iframe\Lib
 */
class Template extends ATemplate
{
    protected $moduleName = 'Iframe';
    protected $templateName = 'iframe';

    protected function fillInputs(): void
    {
        $this->addInput('{PATH}', '');
        $this->addInput('{IFRAME_NOT_SUPPORTED}', Lang::get('iframe.not_supported'));
        $this->addInput('{ALTERNATIVE}', Lang::get('iframe.alternative_link'));
    }

    public function setData(string $path): self
    {
        $this->updateItem('{PATH}', $path);
        return $this;
    }
}
