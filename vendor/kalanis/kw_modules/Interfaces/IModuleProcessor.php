<?php

namespace kalanis\kw_modules\Interfaces;


use kalanis\kw_modules\ModuleException;


/**
 * Class IModuleProcessor
 * @package kalanis\kw_modules\Interfaces
 * Processing settings of modules in storage
 */
interface IModuleProcessor
{
    /**
     * Set which part of site will be loaded here
     * @param int $level
     */
    public function setModuleLevel(int $level): void;

    /**
     * Add new into storage
     * @param string $moduleName
     * @throws ModuleException
     */
    public function add(string $moduleName): void;

    /**
     * Get from storage
     * @param string $moduleName
     * @return IModuleRecord|null
     * @throws ModuleException
     */
    public function get(string $moduleName): ?IModuleRecord;

    /**
     * Get available modules
     * @return string[]
     * @throws ModuleException
     */
    public function listing(): array;

    /**
     * Update in storage
     * @param string $moduleName
     * @param string $params
     * @throws ModuleException
     */
    public function update(string $moduleName, string $params): void;

    /**
     * Remove from storage
     * @param string $moduleName
     * @throws ModuleException
     */
    public function remove(string $moduleName): void;

    /**
     * Save all changes
     * @throws ModuleException
     */
    public function save(): void;
}
