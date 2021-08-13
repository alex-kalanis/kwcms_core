<?php

namespace kalanis\kw_modules\Interfaces;


/**
 * Class IModuleTitle
 * @package kalanis\kw_modules\Interfaces
 * Module interface
 */
interface IModuleTitle extends IModule
{
    /**
     * Return bar title
     * @return string
     */
    public function getTitle(): string;
}
