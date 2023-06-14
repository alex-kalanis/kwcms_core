<?php

namespace KWCMS\modules\Layout\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Loaders\KwLoader;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Raw;
use kalanis\kw_modules\Processing\FileProcessor;
use kalanis\kw_modules\Processing\ModuleRecord;
use kalanis\kw_modules\Processing\Modules;
use kalanis\kw_modules\Processing\Support;
use kalanis\kw_modules\SubModules;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;
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
    /** @var ILoader */
    protected $loader = null;
    /** @var IModule|null */
    protected $module = null;
    /** @var Modules */
    protected $moduleProcessor = null;
    /** @var SubModules */
    protected $subModules = null;

    /**
     * @param ILoader|null $loader
     * @param Modules|null $processor
     * @throws ConfException
     */
    public function __construct(?ILoader $loader, ?Modules $processor)
    {
        Config::load('Core', 'page');
        $this->loader = $loader ?: new KwLoader();
        $this->moduleProcessor = $processor ?: new Modules(new FileProcessor(new ModuleRecord(), Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot() ));
        $this->subModules = new SubModules($this->loader, $this->moduleProcessor);
    }

    public function process(): void
    {
        $this->moduleProcessor->setLevel(ISitePart::SITE_LAYOUT);
        $defaultModuleName = Config::get('Core', 'page.default_display_module', 'Page');
        $wantModuleName = StoreRouted::getPath()->getModule() ?: $defaultModuleName ;
        $moduleRecord = $this->moduleProcessor->readNormalized($wantModuleName);
        $moduleRecord = $moduleRecord ?? $this->moduleProcessor->readNormalized($defaultModuleName);

        if (empty($moduleRecord)) {
            throw new ModuleException(sprintf('Module *%s* not found!', $wantModuleName));
        }

        $extraParams = ('Page' == $moduleRecord->getModuleName() ? [$this->loader, $this->moduleProcessor] : []);
        $this->module = $this->loader->load($moduleRecord->getModuleName(), null, $extraParams);

        if (!$this->module) {
            throw new ModuleException(sprintf('Controller for module *%s* not found!', $moduleRecord->getModuleName()));
        }

        $this->module->init($this->inputs, array_merge(
            Support::paramsIntoArray($moduleRecord->getParams()), $this->params, [ISitePart::KEY_LEVEL => ISitePart::SITE_LAYOUT]
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
        $template = new LayoutTemplate();
        $body = new BodyTemplate();
        $this->subModules->fill($body, $this->inputs, ISitePart::SITE_LAYOUT, $this->params);
        $this->subModules->fill($template, $this->inputs, ISitePart::SITE_LAYOUT, $this->params);
        return $out->setContent($template->setData(
            $useBody ? $body->setData($content->output())->render() : $content->output(),
            ($this->module instanceof IModuleTitle) ? $this->module->getTitle() : ''
        )->render());
    }
}
