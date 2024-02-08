<?php

namespace KWCMS\modules\Transcode\Lib;


use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_input\Interfaces\IFiltered;
use kalanis\kw_modules\Access;
use kalanis\kw_modules\Interfaces;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output;


/**
 * Class Module
 * @package KWCMS\modules\Transcode\Lib
 * Process pages - load correct one and return it
 */
class Module
{
    /** @var IFiltered|null */
    protected $inputs = null;
    /** @param array<string, string|int|float|bool|object> $constructParams  */
    protected $constructParams = [];
    /** @var Interfaces\IModule|null */
    protected $module = null;

    /**
     * @param IFiltered $inputs
     * @param mixed $params
     */
    public function __construct(IFiltered $inputs, $params)
    {
        $this->inputs = $inputs;
        $this->constructParams = $params;
    }

    /**
     * @throws ModuleException
     * @return $this
     */
    public function process(): self
    {
        $modulesFactory = new Access\Factory();
        $this->module = $modulesFactory
            ->getLoader($this->constructParams)
            ->load(['Transcode', 'Transcode'], $this->constructParams);
        $this->module->init($this->inputs, $this->inputs->getInArray(null, [IEntry::SOURCE_GET, IEntry::SOURCE_POST]));
        return $this;
    }

    /**
     * @throws ModuleException
     * @return Output\AOutput
     */
    public function get(): Output\AOutput
    {
        $this->module->process();
        return $this->module->output();
    }
}
