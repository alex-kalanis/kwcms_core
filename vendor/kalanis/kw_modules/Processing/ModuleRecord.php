<?php

namespace kalanis\kw_modules\Processing;


use kalanis\kw_modules\Interfaces\IModuleRecord;
use stdClass;


/**
 * Class ModuleRecord
 * @package kalanis\kw_modules
 * Single record about module
 */
class ModuleRecord extends stdClass implements IModuleRecord
{
    /** @var string */
    protected $moduleName = '';
    /** @var string */
    protected $params = '';

    public function setModuleName(string $moduleName): void
    {
        $this->moduleName = $moduleName;
    }

    public function updateParams(string $params = ''): void
    {
        $this->params = $params;
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    public function getParams(): string
    {
        return $this->params;
    }
}
