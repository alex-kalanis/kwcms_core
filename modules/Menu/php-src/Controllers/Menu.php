<?php

namespace KWCMS\modules\Menu\Controllers;


use kalanis\kw_cache\CacheException;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_cache\Files as CacheStorage;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\MenuFactory;
use kalanis\kw_menu\MoreEntries;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\Linking\ExternalLink;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\Semaphore;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_templates\TemplateException;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Menu\Lib;
use KWCMS\modules\Menu\Lib\Translations;
use KWCMS\modules\Menu\Templates;


/**
 * Class Menu
 * @package KWCMS\modules\Menu\Controllers
 * Menu included as module
 */
class Menu extends AModule
{
    /** @var bool */
    protected $canCache = false;
    /** @var ICache */
    protected $libCache = null;
    /** @var ExternalLink */
    protected $externalLink = null;
    /** @var ITree */
    protected $menuTree = null;
    /** @var Templates\Open */
    protected $tmplOpen = null;
    /** @var Templates\Display */
    protected $tmplDisplay = null;

    /**
     * @throws CacheException
     * @throws ConfException
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     * @throws SemaphoreException
     */
    public function __construct()
    {
        Config::load('Menu');
        $this->canCache = boolval(Config::get('Menu', 'use_cache', false));
        $this->externalLink = new ExternalLink(Stored::getPath(), StoreRouted::getPath());
        $this->tmplOpen = new Templates\Open();
        $this->tmplDisplay = new Templates\Display();
        $innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            boolval(Config::get('Core', 'site.more_lang', false))
        );
        $files = $this->getFiles();
        $this->menuTree = new Lib\Tree($this->getEntriesProcessor($files), $innerLink);
        $this->libCache = $this->getCache($files, $innerLink);
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @return CompositeAdapter
     */
    protected function getFiles(): CompositeAdapter
    {
        return (new Factory(new FilesTranslations()))->getClass(
            Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot()
        );
    }

    /**
     * @param CompositeAdapter $files
     * @param InnerLinks $innerLink
     * @throws CacheException
     * @throws PathsException
     * @throws SemaphoreException
     * @return ICache
     */
    protected function getCache(CompositeAdapter $files, InnerLinks $innerLink): ICache
    {
        $cache = new CacheStorage\Semaphore($files, $this->getSemaphore($files, $innerLink));
        $cache->init(array_merge($innerLink->toFullPath([]), ['Menu']));
        return $cache;
    }

    /**
     * @param CompositeAdapter $files
     * @param InnerLinks $innerLink
     * @throws PathsException
     * @throws SemaphoreException
     * @return ISemaphore
     */
    protected function getSemaphore(CompositeAdapter $files, InnerLinks $innerLink): ISemaphore
    {
        return (new Semaphore\Factory())->getSemaphore([
            'semaphore' => $files,
            'semaphore_root' => array_merge(
                $innerLink->toFullPath([]),
                [strval(Config::get('Menu', 'meta_regen'))]
            ),
        ]);
    }

    /**
     * @param CompositeAdapter $files
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     * @return MoreEntries
     */
    protected function getEntriesProcessor(CompositeAdapter $files): MoreEntries
    {
        return (new MenuFactory(new Translations()))->getMenu($files);
    }

    public function process(): void
    {
    }

    /**
     * @throws TemplateException
     * @return AOutput
     */
    public function output(): AOutput
    {
        $out = new Html();
        try {
            if ($this->canCache) {
                if (!$this->libCache->exists()) {
                    $content = $this->getRendered();
                    $this->libCache->set($content);
                } else {
                    $content = $this->libCache->get();
                }
            } else {
                $content = $this->getRendered();
            }
        } catch (CacheException $ex) {
            // Add logging when done
            $content = $this->getRendered();
        }
        return $out->setContent($content);
    }

    /**
     * @throws TemplateException
     * @return string
     */
    protected function getRendered(): string
    {
        $tmplMain = new Templates\Main();
        $menu = $this->menuTree->output([]);
        $headerContent = $this->addHeader($menu);
        $inputContent = $this->addInputs($menu);
        return $tmplMain->setData($this->tmplOpen->reset()->setData($headerContent . $inputContent)->render())->render();
    }

    /**
     * @param \kalanis\kw_menu\Menu\Menu|null $menu
     * @param string $deepLink
     * @throws TemplateException
     * @return string
     */
    protected function addInputs(?\kalanis\kw_menu\Menu\Menu $menu, string $deepLink = ''): string
    {
        if (empty($menu) || empty($menu->getDisplayCount())) {
            return '';
        }

        $items = $menu->getEntries();
        $result = [];
        for ($i = 1; $i <= $menu->getDisplayCount(); $i++) {
            if (!empty($items[$i])) { # have anything on position?
                $subMenu = '';
                $linkPath = $deepLink . IPaths::SPLITTER_SLASH . $items[$i]->getName();

                if ($items[$i]->canGoSub()) {
                    $headerContent = $this->addHeader($items[$i]->getSubmenu());
                    $inputContent = $this->addInputs($items[$i]->getSubmenu(), $linkPath);
                    $subMenu = $this->tmplOpen->reset()->setData($headerContent . $inputContent)->render();
                }

                $result[] = $this->tmplDisplay->setTemplateName('item')->setData(
                    $items[$i]->getName(),
                    $items[$i]->getTitle(),
                    $this->externalLink->linkVariant($linkPath),
                    $subMenu
                )->render();
            } else {
                $result[] = $this->tmplDisplay->setTemplateName('free')->render();
            }
        }
        return implode('', $result);
    }

    /***
     * @param \kalanis\kw_menu\Menu\Menu|null $menu
     * @throws TemplateException
     * @return string
     */
    protected function addHeader(?\kalanis\kw_menu\Menu\Menu $menu): string
    {
        if (empty($menu) || empty($menu->getDisplayCount())) {
            return '';
        }

        return $this->tmplDisplay
            ->setTemplateName('head')
            ->setData($menu->getName(), $menu->getTitle())
            ->render()
        ;
    }
}
