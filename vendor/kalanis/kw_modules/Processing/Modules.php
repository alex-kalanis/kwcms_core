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
    public function createNormalized(string $moduleName): void
    {
        $this->processor->add(Support::normalizeModuleName($moduleName));
        $this->processor->save();
    }

    /**
     * @param string $moduleName
     * @throws ModuleException
     */
    public function createDirect(string $moduleName): void
    {
        $this->processor->add($moduleName);
        $this->processor->save();
    }

    /**
     * @param string $moduleName
     * @return IModuleRecord|null
     * @throws ModuleException
     */
    public function readNormalized(string $moduleName): ?IModuleRecord
    {
        return $this->processor->get(Support::normalizeModuleName($moduleName));
    }

    /**
     * @param string $moduleName
     * @return IModuleRecord|null
     * @throws ModuleException
     */
    public function readDirect(string $moduleName): ?IModuleRecord
    {
        return $this->processor->get($moduleName);
    }

    /**
     * @param string $moduleName
     * @param string $params
     * @throws ModuleException
     */
    public function updateNormalized(string $moduleName, string $params): void
    {
        $this->processor->update(Support::normalizeModuleName($moduleName), $params);
        $this->processor->save();
    }

    /**
     * @param string $moduleName
     * @param string $params
     * @throws ModuleException
     */
    public function updateDirect(string $moduleName, string $params): void
    {
        $this->processor->update($moduleName, $params);
        $this->processor->save();
    }

    /**
     * @param string $moduleName
     * @throws ModuleException
     */
    public function deleteNormalized(string $moduleName): void
    {
        $this->processor->remove(Support::normalizeModuleName($moduleName));
        $this->processor->save();
    }

    /**
     * @param string $moduleName
     * @throws ModuleException
     */
    public function deleteDirect(string $moduleName): void
    {
        $this->processor->remove($moduleName);
        $this->processor->save();
    }
}
