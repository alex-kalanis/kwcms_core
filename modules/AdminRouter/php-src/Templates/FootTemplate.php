<?php

namespace KWCMS\modules\AdminRouter\Templates;


use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;


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
