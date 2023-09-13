<?php

namespace KWCMS\modules\Core\Libs;


use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_input\Interfaces\IFiltered;
use kalanis\kw_modules\Access;
use kalanis\kw_modules\Interfaces;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output;


/**
 * Class Module
 * @package KWCMS\modules\Core\Libs
 * Process modules - load them and return them
 * Practically that one class you want to load when you initialize KWCMS modules
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
        $this->module = $modulesFactory
            ->getLoader(['modules_loaders' => [$this->constructParams, 'web']])
            ->load($defaultModule, $this->constructParams);
        if (!$this->module) {
            throw new ModuleException(sprintf('Controller for wanted default module *%s* not found', strval(reset($defaultModule))));
        }
        $this->module->init($this->inputs, $this->inputs->getInArray(null, [IEntry::SOURCE_EXTERNAL, IEntry::SOURCE_GET, IEntry::SOURCE_POST]));
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
