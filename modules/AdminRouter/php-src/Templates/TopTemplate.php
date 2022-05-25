<?php

namespace KWCMS\modules\AdminRouter\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;


/**
 * Class TopTemplate
 * @package KWCMS\modules\AdminRouter\Templates
 */
class TopTemplate extends ATemplate
{
    protected $moduleName = 'AdminRouter';
    protected $templateName = 'top';

    protected function fillInputs(): void
    {
        $this->addInput('{TO_MENU}', Lang::get('system.to_menu'));
        $this->addInput('{TO_DOWN}', Lang::get('system.to_down'));
    }
}
