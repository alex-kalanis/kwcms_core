<?php

namespace KWCMS\modules\Router\Lib\Chain;


use kalanis\kw_confs\Config;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\Processing\Support;
use kalanis\kw_paths\Stuff;


/**
 * Class ModulePath
 * @package KWCMS\modules\Router\Lib\Chain
 * Chain of Responsibility for loading routes - module controller is in path
 */
class ModulePath extends AChain
{
    public function getModule(): IModule
    {
        $defaultModuleName = strval(Config::get('Core', 'page.default_display_module', 'Dashboard'));
        $wantModuleName = $this->path->getPath() ?: $defaultModuleName;
        $wantModulesController = Stuff::pathToArray($wantModuleName);
        return $this->moduleInit(
            Support::normalizeNamespacedName(array_shift($wantModulesController)),
            Support::normalizeNamespacedName(Stuff::arrayToPath($wantModulesController))
        );
    }
}
