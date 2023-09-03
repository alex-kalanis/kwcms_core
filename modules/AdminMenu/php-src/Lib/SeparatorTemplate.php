<?php

namespace KWCMS\modules\AdminMenu\Lib;


use KWCMS\modules\Core\Libs\ATemplate;


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
