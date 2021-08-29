<?php

namespace KWCMS\modules\Router;


use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
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
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\Stuff;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_styles\Styles;


/**
 * Class Router
 * @package KWCMS\modules\Router
 * Site's router
 * What to sent back on http level - router to decide which controller will be loaded
 * - parse path from input to get correct controller which will make the page content
 * - then put result from that controller into the page layout
 * @link http://kwcms_core.lemp.test
 */
class Router extends AModule
{
    /** @var ILoader */
    protected $loader = null;
    /** @var IModule|null */
    protected $module = null;
    /** @var Modules */
    protected $moduleProcessor = null;
    /** @var SubModules */
    protected $subModules = null;

    public function __construct(?ILoader $loader, ?Modules $processor)
    {
        Config::load('Core', 'page');
        $this->loader = $loader ?: new KwLoader();
        $this->moduleProcessor = $processor ?: new Modules(new FileProcessor(new ModuleRecord(), Config::getPath()->getDocumentRoot() . Config::getPath()->getPathToSystemRoot() ));
        $this->subModules = new SubModules($this->loader, $this->moduleProcessor);
    }

    public function process(): void
    {
        $this->moduleProcessor->setLevel(ISitePart::SITE_ROUTED);
        $defaultModuleName = Config::get('Core', 'page.default_display_module', 'Dashboard');
        $wantModuleName = Config::getPath()->getPath() ?: $defaultModuleName;
        $wantArray = Stuff::pathToArray($wantModuleName);

        if (0 == count($wantArray)) {
            $wantArray = ['Admin', $defaultModuleName];
        } elseif (1 == count($wantArray)) {
            array_unshift($wantArray, 'Admin');
        }

        try {
            $this->module = $this->subModules->initModule(
                Support::normalizeNamespacedName(Support::normalizeModuleName(array_shift($wantArray))),
                $this->inputs, [], array_merge(
                    $this->params, [ISitePart::KEY_LEVEL => ISitePart::SITE_ROUTED]
                ),
                Support::normalizeNamespacedName(Support::normalizeModuleName(Stuff::arrayToPath($wantArray)))
            );

        } catch (\Throwable $ex) { // Fatal error: Class not found -> output on Dashboard

            Notification::addError($ex->getMessage());
            $this->module = $this->subModules->initModule(
                'Admin',
                $this->inputs, [], array_merge(
                    $this->params, [ISitePart::KEY_LEVEL => ISitePart::SITE_ROUTED]
                ),
                Support::normalizeNamespacedName(Support::normalizeModuleName($defaultModuleName))
            );
        }
        $this->module->process();
    }

    public function output(): AOutput
    {
        $result = $this->module->output();
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
        $template = new RouterTemplate();
        $template->setData(
            $content->output(),
            ($this->module instanceof IModuleTitle) ? $this->module->getTitle() : ''
        );
        $this->subModules->fill($template, $this->inputs, ISitePart::SITE_LAYOUT, $this->params);
        return $out->setContent($template->render());
    }
}
