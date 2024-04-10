<?php

namespace KWCMS\modules\Layout\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_modules\Access\Factory as modules_factory;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\Interfaces\Lists\IModulesList;
use kalanis\kw_modules\Mixer\Processor;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Raw;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Layout\BodyTemplate;
use KWCMS\modules\Layout\LayoutTemplate;


/**
 * Class Layout
 * @package KWCMS\modules\Layout\Controllers
 * Site's layout
 * What to sent back on http level - Layout to fill
 * - parse page making basic structure into blocks and load content of that blocks
 */
class Layout extends AModule
{
    protected ILoader $loader;
    protected ?IModule $module = null;
    protected IModulesList $modulesList;
    protected Processor $subModules;
    /** @param array<string, string|int|float|bool|object> $constructParams  */
    protected array $constructParams = [];

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws ModuleException
     */
    public function __construct(...$constructParams)
    {
        Config::load('Core', 'page');

        $this->constructParams = $constructParams;
        // this part is about module loader, it depends one each server
        $modulesFactory = new modules_factory();
        $this->loader = $modulesFactory->getLoader(['modules_loaders' => [$constructParams, 'web']]);
        $this->modulesList = $modulesFactory->getModulesList($constructParams);
        $this->subModules = new Processor($this->loader, $this->modulesList);
    }

    /**
     * @throws ModuleException
     * @throws PathsException
     */
    public function process(): void
    {
        $this->modulesList->setModuleLevel(ISitePart::SITE_LAYOUT);
        $defaultModule = Stuff::linkToArray(strval(Config::get('Core', 'page.default_display_module', 'Page')));
        $defaultModuleName = strval(reset($defaultModule));
        $wantModule = StoreRouted::getPath()->getModule();
        $wantModuleName = strval(reset($wantModule));
        $moduleRecord = $this->modulesList->get($wantModuleName);
        $moduleRecord = $moduleRecord ?? $this->modulesList->get($defaultModuleName);

        if (empty($moduleRecord)) {
            throw new ModuleException(sprintf('Module *%s* not found!', $wantModuleName));
        }

        $this->module = $this->loader->load([$moduleRecord->getModuleName()], $this->constructParams);

        if (!$this->module) {
            throw new ModuleException(sprintf('Controller for module *%s* not found!', $moduleRecord->getModuleName()));
        }

        $this->module->init($this->inputs, array_merge(
            $moduleRecord->getParams(), $this->params, [ISitePart::KEY_LEVEL => ISitePart::SITE_LAYOUT]
        ));
        $this->module->process();
    }

    public function output(): AOutput
    {
        $result = $this->module->output();
        if ($this->inputIsOnlyHead()) {
            return new Raw(); // empty body for HEAD
        }
        $isSolo = StoreRouted::getPath()->isSingle() || $this->inputWantBeSingle();
        return ($result->canWrap() && !$isSolo) ? $this->wrapped($result) : $result ;
    }

    protected function inputWantBeSingle(): bool
    {
        $inputsSingle = $this->inputs->getInArray('single', [
            IEntry::SOURCE_EXTERNAL, IEntry::SOURCE_CLI, IEntry::SOURCE_POST, IEntry::SOURCE_GET
        ]);
        return !empty($inputsSingle);
    }

    protected function inputIsOnlyHead(): bool
    {
        $inputsRequest = $this->inputs->getInArray('REQUEST_METHOD', [IEntry::SOURCE_SERVER]);
        return !empty($inputsRequest) && ('HEAD' == strtoupper(strval(reset($inputsRequest))));
    }

    /**
     * @param AOutput $content
     * @param bool $useBody
     * @throws ModuleException
     * @return AOutput
     */
    public function wrapped(AOutput $content, bool $useBody = true): AOutput
    {
        $out = new Raw();

        $body = new BodyTemplate();
        $bodyToReplace = $body->reset()->get();
        $bodyUpdated = $this->subModules->fill($bodyToReplace, $this->inputs, ISitePart::SITE_LAYOUT, $this->params, $this->constructParams);
        $body->change($bodyToReplace, $bodyUpdated);

        $layout = new LayoutTemplate();
        $layoutToReplace = $layout->reset()->get();
        $layoutUpdated = $this->subModules->fill($layoutToReplace, $this->inputs, ISitePart::SITE_LAYOUT, $this->params, $this->constructParams);
        $layout->change($layoutToReplace, $layoutUpdated);

        return $out->setContent($layout->setData(
            $useBody
                ? $body->setData($content->output())->render()
                : $content->output(),
            ($this->module instanceof IHasTitle) ? $this->module->getTitle() : ''
        )->render());
    }
}
