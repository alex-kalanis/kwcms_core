<?php

namespace kalanis\kw_modules\Interfaces\Lists;


use kalanis\kw_modules\ModuleException;


/**
 * Class IFile
 * @package kalanis\kw_modules\Interfaces\Lists
 * Where in the system will be the information saved as file
 */
interface IFile
{
    /**
     * Set which level will be called
     * @param int $level
     */
    public function setModuleLevel(int $level): void;

    /**
     * @throws ModuleException
     * @return string formatted data
     */
    public function load(): string;

    /**
     * @param string $records formatted data
     * @throws ModuleException
     * @return bool
     */
    public function save(string $records): bool;
}
