<?php

namespace KWCMS\modules\AdminMenu\Lib;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class SeparatorTemplate
 * @package KWCMS\modules\AdminMenu\Lib
 */
class SeparatorTemplate extends ATemplate
{
    protected string $moduleName = 'AdminMenu';
    protected string $templateName = 'separator';

    protected function fillInputs(): void
    {
    }
}
