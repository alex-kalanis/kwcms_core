<?php

namespace KWCMS\modules\Core\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Access\Factory as modules_factory;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Errors\Controllers\Errors;


/**
 * Class Core
 * @package KWCMS\modules\Core\Controllers
 * Pages core - total basics what will be return
 * Which module will response the query
 */
class Core extends AModule
{
    protected ?AModule $module = null;
    /** @param array<string, string|int|float|bool|object> $constructParams  */
    protected array $constructParams = [];
    protected bool $dumpImmediately = false;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws LangException
     */
    public function __construct(...$constructParams)
    {
        Config::load(static::getClassName(static::class), 'site');
        Lang::load(static::getClassName(static::class));

        $this->constructParams = $constructParams;
        $this->dumpImmediately = boolval(intval(Config::get('Core', 'site.debug', false)));
    }

    /**
     * @throws LangException
     * @throws ModuleException
     * @throws PathsException
     */
    public function process(): void
    {
        $modulesFactory = new modules_factory();
        $modulesList = $modulesFactory->getModulesList($this->constructParams);
        $modulesList->setModuleLevel(ISitePart::SITE_RESPONSE);
        $defaultModule = Stuff::linkToArray(strval(Config::get('Core', 'site.default_display_module', 'Layout')));
        $defaultModuleName = strval(reset($defaultModule));
        $wantModule = StoreRouted::getPath()->getModule();
        $wantModuleName = strval(reset($wantModule));
        $moduleRecord = !empty($wantModuleName) ? $modulesList->get($wantModuleName) : $modulesList->get($defaultModuleName);
        $moduleRecord = $moduleRecord ?: $modulesList->get($defaultModuleName);
        $moduleRecord = $moduleRecord ?: $modulesList->get($defaultModuleName);

        if (empty($moduleRecord)) {
            throw new ModuleException(sprintf('Module *%s* not found, not even *%s*!', $wantModuleName, $defaultModuleName));
        }

        try {
            $this->module = $modulesFactory->getLoader($this->constructParams)->load([$moduleRecord->getModuleName()], $this->constructParams);

            if (!$this->module) {
                throw new ModuleException(sprintf('Controller for module *%s* not found!', $moduleRecord->getModuleName()));
            }

            $this->module->init($this->inputs, array_merge(
                $moduleRecord->getParams(),
                $this->params,
                [ISitePart::KEY_LEVEL => ISitePart::SITE_RESPONSE]
            ));
            $this->module->process();
        } catch (ModuleException $ex) {
            if ($this->dumpImmediately) {
                throw $ex;
            }
            $this->module = new Errors($this->constructParams);
            $this->module->init($this->inputs, array_merge(
                $moduleRecord->getParams(), $this->params, [
                    ISitePart::KEY_LEVEL => ISitePart::SITE_LAYOUT,
                    'error' => $ex->getCode() ?: 500,
                    'error_message' => $ex->getMessage()
                ]
            ));
            $this->module->process();
        }
    }

    public function output(): AOutput
    {
        return $this->module->output();
    }
}
