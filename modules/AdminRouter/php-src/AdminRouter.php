<?php

namespace KWCMS\modules\AdminRouter;


use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Interfaces\IModuleUser;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Loaders\KwLoader;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Raw;
use kalanis\kw_modules\Processing\FileProcessor;
use kalanis\kw_modules\Processing\ModuleRecord;
use kalanis\kw_modules\Processing\Modules;
use kalanis\kw_modules\SubModules;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_styles\Styles;


/**
 * Class AdminRouter
 * @package KWCMS\modules\AdminRouter
 * Admin's router
 * What to sent back on http level - router to decide which controller will be loaded
 * - parse path from input to get correct controller which will make the page content
 * - then put result from that controller into the page layout
 * @link http://kwcms_core.lemp.test
 */
class AdminRouter extends AModule
{
    /** @var ILoader */
    protected $loader = null;
    /** @var IModule|null */
    protected $module = null;
    /** @var Modules */
    protected $moduleProcessor = null;
    /** @var SubModules */
    protected $subModules = null;
    /** @var Lib\Chain\Processor */
    protected $chainProcessor = null;

    public function __construct(?ILoader $loader, ?Modules $processor)
    {
        Config::load('Core', 'page');
        $this->loader = $loader ?: new KwLoader();
        $this->moduleProcessor = $processor ?: new Modules(new FileProcessor(
            new ModuleRecord(),
            Config::getPath()->getDocumentRoot() . Config::getPath()->getPathToSystemRoot()
        ));
        $this->subModules = new SubModules($this->loader, $this->moduleProcessor);
        $this->chainProcessor = new Lib\Chain\Processor();
        $this->orderLookup();
    }

    protected function orderLookup(): void
    {
        $path = Config::getPath();
        $this->chainProcessor->addToChain(new Lib\Chain\ModulePath($this->loader, $path, ISitePart::SITE_ROUTED));
        $this->chainProcessor->addToChain(new Lib\Chain\ModuleDashboard($this->loader, $path, ISitePart::SITE_ROUTED));
        $this->chainProcessor->addToChain(new Lib\Chain\ModuleClass($this->loader, $path, ISitePart::SITE_ROUTED));
        $this->chainProcessor->addToChain(new Lib\Chain\AdminModule($this->loader, $path, ISitePart::SITE_ROUTED));
        $this->chainProcessor->addToChain(new Lib\Chain\AdminDashboard($this->loader, $path, ISitePart::SITE_ROUTED));
    }

    public function process(): void
    {
        $this->moduleProcessor->setLevel(ISitePart::SITE_ROUTED);
        $this->chainProcessor->init($this->inputs, $this->params);
        $this->module = $this->chainProcessor->process();
        $this->module->process();
    }

    public function output(): AOutput
    {
        $result = $this->module->output();
        if ($this->inputIsOnlyHead()) {
            return new Raw(); // empty body for HEAD
        }
        $isSolo = Config::getPath()->isSingle() || $this->inputWantBeSingle();
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
     * @return AOutput
     * @throws ModuleException
     */
    protected function wrapped(AOutput $content): AOutput
    {
        Lang::load('Admin');
        Styles::want('Styles', 'admin/admstyle.css');
        Styles::want('Styles', 'admin/admstylem.css');
        Styles::want('Styles', 'admin/admprint.css');
        Scripts::want('Scripts', 'admin/themes.js');
        $out = new Raw();
        $template = new Templates\RouterTemplate();
        $template->setData(
            $content->output(),
            ($this->module instanceof IModuleTitle) ? $this->module->getTitle() : ''
        );
        if ($this->module instanceof IModuleUser && $this->module->getUser()) {
            $tmplTop = new Templates\TopTemplate();
            $tmplFoot = new Templates\FootTemplate();
            $this->subModules->fill($tmplTop, $this->inputs, ISitePart::SITE_LAYOUT, $this->params);
            $tmplTop = strtr($tmplTop->render(), ['{MENU_USER_NAME}' => $this->module->getUser()->getDisplayName()]);
            $template->setTopRow($tmplTop)->setFootRow($tmplFoot->render());
        }
        $this->subModules->fill($template, $this->inputs, ISitePart::SITE_LAYOUT, $this->params);
        return $out->setContent($template->render());
    }
}
