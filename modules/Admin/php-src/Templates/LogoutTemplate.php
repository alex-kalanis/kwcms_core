<?php

namespace KWCMS\modules\Admin\Templates;


use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class LogoutTemplate
 * @package KWCMS\modules\Admin\Templates
 */
class LogoutTemplate extends ATemplate
{
    protected string $moduleName = 'Admin';
    protected string $templateName = 'logout';

    protected function fillInputs(): void
    {
        $this->addInput('{LOGOUT_NAME}', Lang::get('logout.title'));
        $this->addInput('{LOGOUT_TEXT}', Lang::get('logout.text'));
        $this->addInput('{LOGOUT_LINK}', Lang::get('logout.link'));
    }
}
