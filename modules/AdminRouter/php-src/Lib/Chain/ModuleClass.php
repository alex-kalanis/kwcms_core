<?php

namespace KWCMS\modules\AdminRouter\Lib\Chain;


use kalanis\kw_confs\Config;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Processing\Support;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class ModuleClass
 * @package KWCMS\modules\AdminRouter\Lib\Chain
 * Chain of Responsibility for loading routes - module main class has the same name as module itself
 */
class ModuleClass extends AChain
{
    /**
     * @throws ModuleException
     * @throws PathsException
     * @return IModule
     */
    public function getModule(): IModule
    {
        $defaultModuleName = strval(Config::get('Core', 'page.default_display_module', 'Dashboard'));
        $wantModuleName = $this->path->getPath() ?: Stuff::pathToArray($defaultModuleName);
        return $this->moduleInit(
            Support::normalizeNamespacedName(array_shift($wantModuleName)),
            null
        );
    }
}
