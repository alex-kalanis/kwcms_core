<?php

namespace KWCMS\modules\AdminRouter\Lib\Chain;


use kalanis\kw_confs\Config;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\Processing\Support;
use kalanis\kw_paths\Stuff;


/**
 * Class ModuleClass
 * @package KWCMS\modules\AdminRouter\Lib\Chain
 * Chain of Responsibility for loading routes - module main class has the same name as module itself
 */
class ModuleClass extends AChain
{
    public function getModule(): IModule
    {
        $defaultModuleName = strval(Config::get('Core', 'page.default_display_module', 'Dashboard'));
        $wantModuleName = $this->path->getPath() ?: $defaultModuleName;
        $wantModulesBackupController = Stuff::pathToArray($wantModuleName);
        $sameName = array_shift($wantModulesBackupController);
        return $this->moduleInit(
            Support::normalizeNamespacedName($sameName),
            null
        );
    }
}
