<?php

namespace KWCMS\modules\Errors\Lib;


use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class Template
 * @package KWCMS\modules\Errors\Lib
 */
class Template extends ATemplate
{
    protected string $moduleName = 'Errors';
    protected string $templateName = 'errors';

    protected function fillInputs(): void
    {
        $this->addInput('{ERROR_IMAGE_LINK}', '/ms:sysimage/system/alert.png');
        $this->addInput('{CATCH_ERROR}', Lang::get('error.text'));
        $this->addInput('{ERROR_NUMBER}');
        $this->addInput('{ERROR_DESCRIPTION}');
        $this->addInput('{ERROR_SPECIFIC}');
    }

    public function setData(string $errNo, string $errDesc, string $imgLink, string $specificDescription): self
    {
        $this->updateItem('{ERROR_IMAGE_LINK}', $imgLink);
        $this->updateItem('{ERROR_NUMBER}', $errNo);
        $this->updateItem('{ERROR_DESCRIPTION}', $errDesc);
        $this->updateItem('{ERROR_SPECIFIC}', $specificDescription);
        return $this;
    }
}
