<?php

namespace KWCMS\modules\AdminRouter\Lib\Chain;


use kalanis\kw_confs\Config;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class AdminModule
 * @package KWCMS\modules\AdminRouter\Lib\Chain
 * Chain of Responsibility for loading routes - use admin module for processing
 */
class AdminModule extends AChain
{
    /**
     * @throws ModuleException
     * @throws PathsException
     * @return IModule
     */
    public function getModule(): IModule
    {
        $defaultModuleName = strval(Config::get('Core', 'page.default_display_module', 'Dashboard'));
        $wantModuleName = $this->getModulePath() ?: Stuff::pathToArray($defaultModuleName);
        return $this->moduleInit(array_values(array_filter(array_merge(['Admin'], $wantModuleName))));
    }
}
