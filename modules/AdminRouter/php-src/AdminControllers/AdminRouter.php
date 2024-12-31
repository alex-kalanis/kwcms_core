<?php

namespace KWCMS\modules\AdminRouter\AdminControllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Access\Factory as modules_factory;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\Interfaces\Lists\IModulesList;
use kalanis\kw_modules\Mixer\Processor;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Raw;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_styles\Styles;
use KWCMS\modules\AdminRouter\Lib;
use KWCMS\modules\AdminRouter\Templates;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Interfaces\Modules\IHasUser;
use KWCMS\modules\Core\Libs\AModule;


/**
 * Class AdminRouter
 * @package KWCMS\modules\AdminRouter\AdminControllers
 * Admin's router
 * What to send back on http level - router to decide which controller will be loaded
 * - parse path from input to get correct controller which will make the page content
 * - then put result from that controller into the page layout
 * @link http://kwcms_core.lemp.test
 */
class AdminRouter extends AModule
{
    protected ILoader $loader;
    /** @var IModule|null */
    protected ?IModule $module = null;
    protected IModulesList $modulesList;
    protected Processor $subModules;
    protected Lib\Chain\Processor $chainProcessor;
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

        $this->chainProcessor = new Lib\Chain\Processor();
        $this->orderLookup();
    }

    protected function orderLookup(): void
    {
        $path = StoreRouted::getPath();
        $this->chainProcessor->addToChain(new Lib\Chain\ModulePath($this->loader, $path, ISitePart::SITE_ROUTED, $this->constructParams));
        $this->chainProcessor->addToChain(new Lib\Chain\ModuleDashboard($this->loader, $path, ISitePart::SITE_ROUTED, $this->constructParams));
        $this->chainProcessor->addToChain(new Lib\Chain\ModuleClass($this->loader, $path, ISitePart::SITE_ROUTED, $this->constructParams));
        $this->chainProcessor->addToChain(new Lib\Chain\AdminModule($this->loader, $path, ISitePart::SITE_ROUTED, $this->constructParams));
        $this->chainProcessor->addToChain(new Lib\Chain\AdminDashboard($this->loader, $path, ISitePart::SITE_ROUTED, $this->constructParams));
    }

    public function process(): void
    {
        $this->modulesList->setModuleLevel(ISitePart::SITE_ROUTED);
        $this->chainProcessor->init($this->inputs, $this->params);
        $this->module = $this->chainProcessor->process();
        $this->module->process();
    }

    /**
     * @throws LangException
     * @throws ModuleException
     * @return AOutput
     */
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
     * @throws LangException
     * @throws ModuleException
     * @return AOutput
     */
    protected function wrapped(AOutput $content): AOutput
    {
        Lang::load('Admin');
        Styles::want('admin', 'admstyle.css');
        Styles::want('admin', 'admstylem.css');
        Styles::want('admin', 'admprint.css');
        Scripts::want('admin', 'themes.js');
        $out = new Raw();
        $template = new Templates\RouterTemplate();
        $template->setData(
            $content->output(),
            ($this->module instanceof IHasTitle) ? $this->module->getTitle() : ''
        );
        if ($this->module instanceof IHasUser && $this->module->getUser()) {
            $tmplTop = new Templates\TopTemplate();
            $tmplFoot = new Templates\FootTemplate();
            $top = $this->subModules->fill($tmplTop->reset()->get(), $this->inputs, ISitePart::SITE_LAYOUT, $this->params, $this->constructParams);
            $tmplTop->change($tmplTop->reset()->get(), $top);
            $tmplTop = strtr($tmplTop->render(), ['{MENU_USER_NAME}' => $this->module->getUser()->getDisplayName()]);
            $template->setTopRow($tmplTop)->setFootRow($tmplFoot->render());
        }
        $tmpl = $this->subModules->fill($template->get(), $this->inputs, ISitePart::SITE_LAYOUT, $this->params, $this->constructParams);
        $template->change($template->get(), $tmpl);
        return $out->setContent($template->render());
    }
}
