<?php

namespace kalanis\kw_modules\Interfaces;


/**
 * Class IModuleRecord
 * @package kalanis\kw_modules\Interfaces
 * Record about module - interface
 * We can have it separated or inside the database
 */
interface IModuleRecord
{
    /**
     * @param string $moduleName which module name will be updated
     */
    public function setModuleName(string $moduleName): void;

    /**
     * Update module's default params
     * @param string $params
     */
    public function updateParams(string $params = ''): void;

    /**
     * Get module name
     * @return string
     */
    public function getModuleName(): string;

    /**
     * Get default params for module
     * @return string
     */
    public function getParams(): string;
}
