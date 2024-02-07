<?php

namespace KWCMS\modules\Krep\Libs;


use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_input\Interfaces\IFiltered;
use kalanis\kw_modules\Access;
use kalanis\kw_modules\Interfaces;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output;


/**
 * Class Module
 * @package KWCMS\modules\Krep\Libs
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
     * @param string[] $defaultModule
     * @throws ModuleException
     * @return $this
     */
    public function process(array $defaultModule): self
    {
        $modulesFactory = new Access\Factory();
        $limitedPath = ['Krep', $this->currentPath($defaultModule)];
        $this->module = $modulesFactory
            ->getLoader($this->constructParams)
            ->load($limitedPath, $this->constructParams);
        if (!$this->module) {
            throw new ModuleException(sprintf('Controller for wanted default module *%s* not found', strval(reset($defaultModule))));
        }
        $this->module->init($this->inputs, $this->inputs->getInArray(null, [IEntry::SOURCE_GET, IEntry::SOURCE_POST]));
        return $this;
    }

    protected function currentPath(array $defaultModule): string
    {
        // get current path from URL
        // then select correct page
        return reset($defaultModule);
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
