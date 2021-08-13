<?php

namespace kalanis\kw_modules\Processing;


use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Interfaces\IModuleProcessor;
use kalanis\kw_modules\Interfaces\IModuleRecord;


/**
 * Class Modules
 * @package kalanis\kw_modules
 * Processing over modules - CRUD
 */
class Modules
{
    protected $processor = null;

    public function __construct(IModuleProcessor $processor)
    {
        $this->processor = $processor;
    }

    public function setLevel(int $level): void
    {
        $this->processor->setModuleLevel($level);
    }

    /**
     * @return string[]
     * @throws ModuleException
     */
    public function listing(): array
    {
        return $this->processor->listing();
    }

    /**
     * @param string $moduleName
     * @throws ModuleException
     */
    public function create(string $moduleName): void
    {
        $this->processor->add(Support::normalizeModuleName($moduleName));
        $this->processor->save();
    }

    /**
     * @param string $moduleName
     * @return IModuleRecord|null
     * @throws ModuleException
     */
    public function read(string $moduleName): ?IModuleRecord
    {
        return $this->processor->get(Support::normalizeModuleName($moduleName));
    }

    /**
     * @param string $moduleName
     * @param string $params
     * @throws ModuleException
     */
    public function update(string $moduleName, string $params): void
    {
        $this->processor->update(Support::normalizeModuleName($moduleName), $params);
        $this->processor->save();
    }

    /**
     * @param string $moduleName
     * @throws ModuleException
     */
    public function delete(string $moduleName): void
    {
        $this->processor->remove(Support::normalizeModuleName($moduleName));
        $this->processor->save();
    }
}
