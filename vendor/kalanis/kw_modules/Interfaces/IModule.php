<?php

namespace kalanis\kw_modules\Interfaces;


use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output\AOutput;


/**
 * Class IModule
 * @package kalanis\kw_modules\Interfaces
 * Module interface
 */
interface IModule
{
    const MODULE_DISABLED = 0; # module manually disabled
    const MODULE_ENABLED = 1; # module can be run
    const MODULE_NO_DRIVE_CLASS = 2; # module did not have class to run

    /**
     * Initialize module, set values from external sources
     * @param IVariables $inputs from external sources
     * @param array $passedParams from modules settings
     * @return void
     */
    public function init(IVariables $inputs, array $passedParams): void;

    /**
     * Process things in module
     * @throws ModuleException
     */
    public function process(): void;

    /**
     * Return what will be presented
     * @return AOutput
     * @throws ModuleException
     */
    public function output(): AOutput;
}
