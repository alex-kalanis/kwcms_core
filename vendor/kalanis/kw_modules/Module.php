<?php

namespace kalanis\kw_modules;


use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_input\Interfaces\IInputs;
use kalanis\kw_input\Variables;
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
    /** @var Interfaces\IModule|null */
    protected $module = null;

    /**
     * @param IInputs $inputs
     * @param Modules|null $processor
     * @param Interfaces\ILoader|null $loader
     * @throws ModuleException
     */
    public function __construct(IInputs $inputs, ?Modules $processor, ?Interfaces\ILoader $loader = null)
    {
        $loader = $loader ?: new KwLoader();
        $processor = $processor ?: new Modules(new FileProcessor(Config::getPath(), new ModuleRecord()));

        $inputHelper = new Variables($inputs);
        $this->module = $loader->load('Core', null, [$loader, $processor]);
        $this->module->init($inputHelper, $inputHelper->getInArray(null, [IEntry::SOURCE_EXTERNAL]));
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
