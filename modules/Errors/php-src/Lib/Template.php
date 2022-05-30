<?php

namespace KWCMS\modules\Errors\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class Template
 * @package KWCMS\modules\Errors\Lib
 */
class Template extends ATemplate
{
    protected $moduleName = 'Errors';
    protected $templateName = 'errors';

    protected function fillInputs(): void
    {
        $this->addInput('{ERROR_IMAGE_LINK}', '/ms:sysimage/alert.png');
        $this->addInput('{CATCH_ERROR}', Lang::get('error.text'));
        $this->addInput('{ERROR_NUMBER}');
        $this->addInput('{ERROR_DESCRIPTION}');
    }

    public function setData(string $errNo, string $errDesc): self
    {
        $this->updateItem('{ERROR_NUMBER}', $errNo);
        $this->updateItem('{ERROR_DESCRIPTION}', $errDesc);
        return $this;
    }
}
