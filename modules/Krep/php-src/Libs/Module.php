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
    protected IFiltered $inputs;
    protected Access\Factory $modulesFactory;
    /** @param array<string, string|int|float|bool|object> $constructParams  */
    protected array $constructParams = [];
    /** @var Interfaces\IModule */
    protected ?Interfaces\IModule $module = null;

    /**
     * @param IFiltered $inputs
     * @param Access\Factory $modulesFactory
     * @param mixed $params
     */
    public function __construct(IFiltered $inputs, Access\Factory $modulesFactory, $params)
    {
        $this->inputs = $inputs;
        $this->modulesFactory = $modulesFactory;
        $this->constructParams = $params;
    }

    /**
     * @param string[] $defaultModule
     * @throws ModuleException
     * @return $this
     */
    public function process(array $defaultModule): self
    {
        $limitedPath = ['Krep', $this->currentPath($defaultModule)];
        $this->module = $this->modulesFactory
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
