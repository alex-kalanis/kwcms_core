<?php

namespace kalanis\kw_modules;


use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_modules\Loaders\KwLoader;
use kalanis\kw_modules\Processing\FileProcessor;
use kalanis\kw_modules\Processing\ModuleRecord;
use kalanis\kw_modules\Processing\Modules;


/**
 * Class Module
 * @package kalanis\kw_modules
 * Process modules - load them and return them
 * Practically that one class you want to load when you initialize KWCMS modules
 */
class Module
{
    /** @var IVariables|null */
    protected $inputs = null;
    /** @var Interfaces\ILoader|null */
    protected $loader = null;
    /** @var Modules|null|null */
    protected $processor = null;
    /** @var Interfaces\IModule|null */
    protected $module = null;

    /**
     * @param IVariables $inputs
     * @param string $moduleDefinitionDir
     * @param Modules|null $processor
     * @param Interfaces\ILoader|null $loader
     * @throws ModuleException
     */
    public function __construct(IVariables $inputs, string $moduleDefinitionDir, ?Modules $processor, ?Interfaces\ILoader $loader = null)
    {
        $this->inputs = $inputs;
        $this->loader = $loader ?: new KwLoader();
        if (empty($moduleDefinitionDir) && empty($processor)) {
            throw new ModuleException('You must set at least directory with module definitions or the whole processor itself');
        }
        $this->processor = $processor ?: new Modules(new FileProcessor(new ModuleRecord(), $moduleDefinitionDir));
    }

    /**
     * @param string $defaultModule
     * @return $this
     * @throws ModuleException
     */
    public function process($defaultModule = 'Core'): self
    {
        $this->module = $this->loader->load($defaultModule, null, [$this->loader, $this->processor]);
        $this->module->init($this->inputs, $this->inputs->getInArray(null, [IEntry::SOURCE_EXTERNAL]));
        return $this;
    }

    /**
     * @return Output\AOutput
     * @throws ModuleException
     */
    public function get(): Output\AOutput
    {
        $this->module->process();
        return $this->module->output();
    }
}
