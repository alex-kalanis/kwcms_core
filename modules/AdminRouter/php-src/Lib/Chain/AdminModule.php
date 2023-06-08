<?php

namespace KWCMS\modules\AdminRouter\Lib\Chain;


use kalanis\kw_confs\Config;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\Processing\Support;
use kalanis\kw_paths\Stuff;


/**
 * Class AdminModule
 * @package KWCMS\modules\AdminRouter\Lib\Chain
 * Chain of Responsibility for loading routes - use admin module for processing
 */
class AdminModule extends AChain
{
    public function getModule(): IModule
    {
        $defaultModuleName = strval(Config::get('Core', 'page.default_display_module', 'Dashboard'));
        $wantModuleName = $this->path->getPath() ?: Stuff::pathToArray($defaultModuleName);
        return $this->moduleInit(
            'Admin',
            Support::normalizeNamespacedName(Stuff::arrayToPath($wantModuleName))
        );
    }
}
