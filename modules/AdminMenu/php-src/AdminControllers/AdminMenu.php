<?php

namespace KWCMS\modules\AdminMenu\AdminControllers;


use kalanis\kw_address_handler\Handler;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_modules\Access\Factory;
use kalanis\kw_modules\Interfaces\Lists\IModulesList;
use kalanis\kw_modules\Interfaces\Lists\ISitePart;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\ModulesLists\Record;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_routed_paths\Support;
use KWCMS\modules\AdminMenu\Lib;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Class AdminMenu
 * @package KWCMS\modules\AdminMenu\AdminControllers
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
    /** @var ExternalLink */
    protected $externalLink = null;
    /** @var IModulesList */
    protected $modulesList = null;

    /** @var Lib\LineTemplate */
    protected $tmplLine = null;
    /** @var Lib\SeparatorTemplate */
    protected $tmplSep = null;
    /** @var Lib\ListingTemplate */
    protected $tmplListing = null;

    /** @var string */
    protected $menuKey = '';
    /** @var int */
    protected $maxPos = 0;
    /** @var Record[] */
    protected $entries = [];

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws ModuleException
     */
    public function __construct(...$constructParams)
    {
        Config::load('Core', 'page');
        Config::load('Menu');
        $this->externalLink = new ExternalLink(StoreRouted::getPath());
        $this->modulesList = (new Factory())->getModulesList($constructParams);
        $this->tmplLine = new Lib\LineTemplate();
        $this->tmplSep = new Lib\SeparatorTemplate();
        $this->tmplListing = new Lib\ListingTemplate();
    }

    public function process(): void
    {
        // list only enabled for currently set menu
        $this->modulesList->setModuleLevel(ISitePart::SITE_ROUTED);
        $this->menuKey = strval($this->getFromParam('menuKey', ''));
        $allModules = $this->modulesList->listing();
        if (!empty($allModules)) {
            $allModules = array_filter($allModules, [$this, 'filterMenu']); // filter in correct menu
            $allModules = array_filter($allModules, [$this, 'filterDisabled']); // filter out disabled
            $this->maxPos = max(array_map([$this, 'getPos'], $allModules)); // available max position
            $this->maxPos++;
            array_walk($allModules, [$this, 'addPos']); // set positions for the rest
            usort($allModules, [$this, 'sortByPos']); // sort by positions
            $this->entries = array_combine(array_map([$this, 'getPos'], $allModules), $allModules);
        }
    }

    public function filterMenu(Record $module): bool
    {
        $params = $module->getParams();
        if (!isset($params['menu']) && empty($this->menuKey)) {
            return true;
        }
        return (isset($params['menu']) && $this->menuKey == $params['menu']);
    }

    public function filterDisabled(Record $module): bool
    {
        $params = $module->getParams();
        return ((!isset($params['display'])) || ('no' != $params['display'])) && $module->isEnabled();
    }

    public function getPos(Record $module): int
    {
        $params = $module->getParams();
        return isset($params['pos']) ? intval($params['pos']) : 0 ;
    }

    public function addPos(Record $module): void
    {
        $params = $module->getParams();
        $params['pos'] = empty($params['pos'])
            ? $this->maxPos++
            : intval($params['pos'])
        ;
        $module->setParams($params);
    }

    public function sortByPos(Record $moduleA, Record $moduleB): int
    {
        return $moduleA->getParams()['pos'] <=> $moduleB->getParams()['pos'];
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

    protected function getLinkPath(Record $module): string
    {
        $params = $module->getParams();
        return empty($params['link']) ? Support::linkModuleName($module->getModuleName()) : $params['link'] ;
    }

    protected function getName(Record $module): string
    {
        $params = $module->getParams();
        return empty($params['name']) ? $module->getModuleName() : $params['name'] ;
    }

    protected function getStyle(Record $module): string
    {
        $params = $module->getParams();
        $styles = empty($params['style']) ? '' : $params['style'];
        $styleArray = Handler::http_parse_query(strtr($styles, [':' => '=']), ';'); // CSS to http links hack
        if (!empty($params['image'])) {
            $styleArray['background-image'] = sprintf("url('%s')", $this->externalLink->linkVariant($params['image'], 'sysimage', true));
        }
        return strtr(urldecode(http_build_query($styleArray, '', ';')), ['=' => ':']); // restore to CSS
    }
}
