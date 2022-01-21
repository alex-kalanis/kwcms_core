<?php

namespace KWCMS\modules\AdminMenu;


use kalanis\kw_address_handler\Handler;
use kalanis\kw_confs\Config;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\IModuleRecord;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_modules\Processing\FileProcessor;
use kalanis\kw_modules\Processing\ModuleRecord;
use kalanis\kw_modules\Processing\Modules;
use kalanis\kw_modules\Processing\Support;


/**
 * Class AdminMenu
 * @package KWCMS\modules\AdminMenu
 *
 * - got modules for admin and add them to the menu
 * - need menu id; empty is into default
 * - the data passed as from the other modules within templates - library \kalanis\kw_modules\Processing\Modules
 * - from there it filter available to display
 * - as plus it need order of entries, so it's possible to draw them correctly
 *   - unsorted go in the end as they are in module config file
 * - it have tons of support configs (icon, description, for what, statuses like disabled, devel, test)
 *
 * - adding and removing of modules is possible by central way; now by hand
 *   - this depends on data sources and the whole fun with the repositories (like in composer)
 *     - repos wants data dump via different formats like git repo, zip or gzip packages, files itself, ...
 *     - in any case kwcms repos have extra configuration file which describes that package
 *       - and most of my projects has it already in some way or another
 */
class AdminMenu extends AModule
{
    protected $externalLink = null;
    /** @var Modules */
    protected $moduleProcessor = null;

    protected $tmplLine = null;
    protected $tmplSep = null;
    protected $tmplListing = null;

    protected $menuKey = '';
    protected $maxPos = 0;
    /** @var IModuleRecord[] */
    protected $entries = [];

    public function __construct(?ILoader $loader = null, ?Modules $processor = null)
    {
        Config::load('Core', 'page');
        Config::load('Menu');
        $this->externalLink = new ExternalLink(Config::getPath());
        $this->moduleProcessor = $processor ?: new Modules($this->getCorrectProcessor());
        $this->tmplLine = new Lib\LineTemplate();
        $this->tmplSep = new Lib\SeparatorTemplate();
        $this->tmplListing = new Lib\ListingTemplate();
    }

    protected function getCorrectProcessor(): FileProcessor
    {
        return new \ExProcessor(new ModuleRecord(),  $this->getCorrectConfPath() );
    }

    protected function getCorrectConfPath(): string
    {
        return Config::getPath()->getDocumentRoot() . Config::getPath()->getPathToSystemRoot();
    }

    public function process(): void
    {
        // list only enabled for currently set menu
        $this->moduleProcessor->setLevel(ISitePart::SITE_ROUTED);
        $this->menuKey = (string)$this->getFromParam('menuKey', '');
        $listModules = $this->moduleProcessor->listing();
        $allModules = array_combine($listModules, array_map([$this, 'readModule'], $listModules));
        $allModules = array_filter($allModules); // now we have only possible ones
        if (!empty($allModules)) {
            array_walk($allModules, [$this, 'addDecodedParams']); // add key with decoded params
            $allModules = array_filter($allModules, [$this, 'filterMenu']); // filter in correct menu
            $allModules = array_filter($allModules, [$this, 'filterDisabled']); // filter out disabled
            $this->maxPos = max(array_map([$this, 'getPos'], $allModules)); // available max position
            $this->maxPos++;
            array_walk($allModules, [$this, 'addPos']); // set positions for the rest
            usort($allModules, [$this, 'sortByPos']); // sort by positions
            $this->entries = array_combine(array_map([$this, 'getPos'], $allModules), $allModules);
        }
    }

    /**
     * @param string $moduleName
     * @return IModuleRecord|null
     * @throws ModuleException
     */
    public function readModule(string $moduleName): ?IModuleRecord
    {
        return $this->moduleProcessor->readDirect($moduleName);
    }

    public function addDecodedParams(IModuleRecord $module): void
    {
        $module->parsedParams = $this->parseParams($module->getParams());
    }

    public function filterMenu(IModuleRecord $module): bool
    {
        return (empty($module->parsedParams['menu']) && empty($this->menuKey))
            || (!empty($module->parsedParams['menu']) && $this->menuKey == $module->parsedParams['menu']);
    }

    public function filterDisabled(IModuleRecord $module): bool
    {
        return (!isset($module->parsedParams['display'])) || ('no' != $module->parsedParams['display']);
    }

    public function getPos(IModuleRecord $module): int
    {
        return isset($module->parsedParams['pos']) ? intval($module->parsedParams['pos']) : 0 ;
    }

    public function addPos(IModuleRecord $module): void
    {
        $module->parsedParams['pos'] = empty($module->parsedParams['pos'])
            ? $this->maxPos++
            : intval($module->parsedParams['pos'])
        ;
    }

    public function sortByPos(IModuleRecord $moduleA, IModuleRecord $moduleB): int
    {
        return $moduleA->parsedParams['pos'] <=> $moduleB->parsedParams['pos'];
    }

    public function output(): AOutput
    {
        $this->tmplListing->setData(
            '/',
            $this->getTopClass(),
            $this->getTopName()
        );
        for ($i = 1; $i < $this->maxPos; $i++) {
            if (isset($this->entries[$i])) {
                $this->tmplLine->reset()->setData(
                    $this->externalLink->linkVariant($this->getLinkPath($this->entries[$i])),
                    $this->getName($this->entries[$i]),
                    $this->getStyle($this->entries[$i])
                );
                $this->tmplListing->addSubEntry($this->tmplLine->render());
            } else {
                $this->tmplListing->addSubEntry($this->tmplSep->render());
            }
        }
        $out = new Html();
        return $out->setContent($this->tmplListing->render());
    }

    protected function getTopClass(): string
    {
        return $this->getFromParam('topClass', 'menu_user prime');
    }

    protected function getTopName(): string
    {
        return $this->getFromParam('topName', '{MENU_USER_NAME}');
    }

    protected function getLinkPath(IModuleRecord $module): string
    {
        return empty($module->parsedParams['link']) ? Support::linkModuleName($module->getModuleName()) : $module->parsedParams['link'] ;
    }

    protected function getName(IModuleRecord $module): string
    {
        return empty($module->parsedParams['name']) ? $module->getModuleName() : $module->parsedParams['name'] ;
    }

    protected function getStyle(IModuleRecord $module): string
    {
        $styles = empty($module->parsedParams['style']) ? '' : $module->parsedParams['style'];
        $styleArray = Handler::http_parse_query(strtr($styles, [':' => '=']), ';'); // CSS to http links hack
        if (!empty($module->parsedParams['image'])) {
            $styleArray['background-image'] = sprintf("url('%s')", $this->externalLink->linkVariant($module->parsedParams['image'], 'sysimage', true));
        }
        return strtr(urldecode(http_build_query($styleArray, null, ';')), ['=' => ':']); // restore to CSS
    }

    protected function parseParams(string $params): array
    {
        return Handler::http_parse_query($params);
    }

    protected function addToParam(string $params, array $toAdd): string
    {
        $arrayOfParams = $this->parseParams($params);
        $arrayOfParams += $toAdd;
        return http_build_query($arrayOfParams);
    }
}
