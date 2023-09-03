<?php

namespace KWCMS\modules\Core\Interfaces\Modules;


use kalanis\kw_modules\Interfaces\IModule;


/**
 * Class IHasTitle
 * @package KWCMS\modules\Core\Interfaces\Modules
 * Module has title
 */
interface IHasTitle extends IModule
{
    /**
     * Return bar title
     * @return string
     */
    public function getTitle(): string;
}
