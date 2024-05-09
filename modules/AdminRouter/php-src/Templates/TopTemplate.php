<?php

namespace KWCMS\modules\AdminRouter\Templates;


use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class TopTemplate
 * @package KWCMS\modules\AdminRouter\Templates
 */
class TopTemplate extends ATemplate
{
    protected string $moduleName = 'AdminRouter';
    protected string $templateName = 'top';

    protected function fillInputs(): void
    {
        $this->addInput('{TO_MENU}', Lang::get('system.to_menu'));
        $this->addInput('{TO_DOWN}', Lang::get('system.to_down'));
    }
}
