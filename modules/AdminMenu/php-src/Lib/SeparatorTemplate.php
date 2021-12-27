<?php

namespace KWCMS\modules\AdminMenu\Lib;


use kalanis\kw_modules\ATemplate;


/**
 * Class SeparatorTemplate
 * @package KWCMS\modules\AdminMenu\Lib
 */
class SeparatorTemplate extends ATemplate
{
    protected $moduleName = 'AdminMenu';
    protected $templateName = 'separator';

    protected function fillInputs(): void
    {
    }
}
