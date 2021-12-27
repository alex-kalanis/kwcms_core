<?php

namespace KWCMS\modules\AdminRouter\Lib\Chain;


use kalanis\kw_confs\Config;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\Processing\Support;


/**
 * Class AdminDashboard
 * @package KWCMS\modules\AdminRouter\Lib\Chain
 * Chain of Responsibility for loading routes - use main admin module for processing
 */
class AdminDashboard extends AChain
{
    public function getModule(): IModule
    {
        $defaultModuleName = strval(Config::get('Core', 'page.default_display_module', 'Dashboard'));
        return $this->moduleInit(
            'Admin',
            Support::normalizeNamespacedName($defaultModuleName)
        );
    }
}
