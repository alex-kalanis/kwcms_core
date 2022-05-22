<?php

namespace KWCMS\modules\AdminRouter\Lib\Chain;


use kalanis\kw_confs\Config;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\Processing\Support;
use kalanis\kw_paths\Stuff;


/**
 * Class ModuleDashboard
 * @package KWCMS\modules\AdminRouter\Lib\Chain
 * Chain of Responsibility for loading routes - module main class is Dashboard
 */
class ModuleDashboard extends AChain
{
    public function getModule(): IModule
    {
        $defaultModuleName = strval(Config::get('Core', 'page.default_display_module', 'Dashboard'));
        $wantModuleName = $this->path->getPath() ?: $defaultModuleName;
        $wantModulesMainController = Stuff::pathToArray($wantModuleName);
        $sameName = array_shift($wantModulesMainController);
        return $this->moduleInit(
            Support::normalizeNamespacedName($sameName),
            Support::normalizeNamespacedName($defaultModuleName)
        );
    }
}
