<?php

namespace KWCMS\modules\Core\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Loaders\KwLoader;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Processing\FileProcessor;
use kalanis\kw_modules\Processing\ModuleRecord;
use kalanis\kw_modules\Processing\Modules;
use kalanis\kw_modules\Processing\Support;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;


/**
 * Class Core
 * @package KWCMS\modules\Core\Controllers
 * Pages core - total basics what will be return
 * Which module will response the query
 */
class Core extends AModule
{
    /** @var ILoader */
    protected $loader = null;
    /** @var AModule|null */
    protected $module = null;
    /** @var Modules */
    protected $moduleProcessor = null;

    /**
     * @param ILoader|null $loader
     * @param Modules|null $processor
     * @throws ConfException
     * @throws LangException
     */
    public function __construct(?ILoader $loader, ?Modules $processor)
    {
        Config::load(static::getClassName(static::class), 'site');
        Lang::load(static::getClassName(static::class));
        $this->loader = $loader ?: new KwLoader();
        $this->moduleProcessor = $processor ?: new Modules(new FileProcessor(new ModuleRecord(), Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot() ));
    }

    public function process(): void
    {
        $this->moduleProcessor->setLevel(ISitePart::SITE_RESPONSE);
        $defaultModuleName = Config::get('Core', 'site.default_display_module', 'Layout');
        $wantModuleName = StoreRouted::getPath()->getModule();
        $moduleRecord = $wantModuleName ? $this->moduleProcessor->readNormalized($wantModuleName) : $this->moduleProcessor->readDirect($defaultModuleName);
        $moduleRecord = $moduleRecord ?: $this->moduleProcessor->readNormalized($defaultModuleName);
        $moduleRecord = $moduleRecord ?: $this->moduleProcessor->readDirect($defaultModuleName);

        if (empty($moduleRecord)) {
            throw new ModuleException(sprintf('Module *%s* not found, not even *%s*!', $wantModuleName, $defaultModuleName));
        }

        $this->module = $this->loader->load($moduleRecord->getModuleName(), null,
            (in_array($moduleRecord->getModuleName(), $this->modulesWithPassingParams()) ? [$this->loader, $this->moduleProcessor] : [])
        );

        if (!$this->module) {
            throw new ModuleException(sprintf('Controller for module *%s* not found!', $moduleRecord->getModuleName()));
        }

        $this->module->init($this->inputs, array_merge(
            Support::paramsIntoArray($moduleRecord->getParams()),
            $this->params,
            [ISitePart::KEY_LEVEL => ISitePart::SITE_RESPONSE]
        ));
        $this->module->process();
    }

    /**
     * Real modules with basic templates - admin, page, ...
     * @return string[]
     */
    protected function modulesWithPassingParams(): array
    {
        return ['Layout', 'AdminRouter', 'SinglePage', 'Image', 'Pedigree'];
    }

    public function output(): AOutput
    {
        return $this->module->output();
    }
}
