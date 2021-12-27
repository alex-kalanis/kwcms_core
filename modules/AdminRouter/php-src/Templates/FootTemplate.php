<?php

namespace KWCMS\modules\AdminRouter\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;


/**
 * Class FootTemplate
 * @package KWCMS\modules\AdminRouter\Templates
 */
class FootTemplate extends ATemplate
{
    protected $moduleName = 'AdminRouter';
    protected $templateName = 'foot';

    protected function fillInputs(): void
    {
        $this->addInput('{TO_UP}', Lang::get('system.to_up'));
    }
}
