<?php

namespace kalanis\kw_modules\Interfaces\Lists;


use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\ModulesLists\Record;


/**
 * Class IModulesList
 * @package kalanis\kw_modules\Interfaces\Lists
 * Processing settings of modules in storage
 */
interface IModulesList
{
    /**
     * Set which part of site will be loaded here
     * @param int $level
     */
    public function setModuleLevel(int $level): void;

    /**
     * Add new module config into storage
     * @param string $moduleName
     * @param bool $enabled
     * @param string[] $params
     * @throws ModuleException
     * @return bool
     */
    public function add(string $moduleName, bool $enabled = false, array $params = []): bool;

    /**
     * Get module config from storage
     * @param string $moduleName
     * @throws ModuleException
     * @return Record|null
     */
    public function get(string $moduleName): ?Record;

    /**
     * Get available modules
     * @throws ModuleException
     * @return array<string, Record>
     */
    public function listing(): array;

    /**
     * Update module config in storage
     * Null means no change
     * @param string $moduleName
     * @param bool|null $enabled
     * @param string[]|null $params
     * @throws ModuleException
     * @return bool
     */
    public function updateBasic(string $moduleName, ?bool $enabled, ?array $params): bool;

    /**
     * Update in storage
     * @param Record $record
     * @throws ModuleException
     * @return bool
     */
    public function updateObject(Record $record): bool;

    /**
     * Remove from storage
     * @param string $moduleName
     * @throws ModuleException
     * @return bool
     */
    public function remove(string $moduleName): bool;
}
