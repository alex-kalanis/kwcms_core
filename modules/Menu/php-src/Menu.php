<?php

namespace KWCMS\modules\Menu;


use kalanis\kw_confs\Config;
use kalanis\kw_cache\Storage as CacheStorage;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_modules\InternalLink;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\Semaphore;
use kalanis\kw_storage\Storage;
use kalanis\kw_storage\StorageException;


/**
 * Class Menu
 * @package KWCMS\modules\Menu
 * Menu included as module
 */
class Menu extends AModule
{
    protected $canCache = false;
    protected $libCache = null;
    protected $externalLink = null;
    protected $internalLink = null;
    protected $tree = null;
    protected $tmplOpen = null;
    protected $tmplDisplay = null;

    public function __construct()
    {
        Config::load('Menu');
        $this->canCache = (bool)Config::get('Menu', 'use_cache', false);
        $this->externalLink = new ExternalLink(Config::getPath());
        $this->internalLink = new InternalLink(Config::getPath());
        $this->tree = new Lib\Tree($this->internalLink);
        $this->tmplOpen = new Templates\Open();
        $this->tmplDisplay = new Templates\Display();
        $this->libCache = $this->getCache();
    }

    protected function getCache(): ICache
    {
        $cachePath = Config::getPath()->getDocumentRoot() . DIRECTORY_SEPARATOR . IPaths::DIR_TEMP;
        Storage\Key\DirKey::setDir($cachePath. DIRECTORY_SEPARATOR);
        $storage = new Storage\Factory(new Storage\Target\Factory(), new Storage\Format\Factory(), new Storage\Key\Factory());
        $cache = new CacheStorage\Semaphore($storage->getStorage('volume'), $this->getSemaphore());
        $cache->init('Menu');
        return $cache;
    }

    protected function getSemaphore(): ISemaphore
    {
        return new Semaphore\Volume($this->internalLink->userContent(
            Config::get('Menu', 'meta_regen'), true, false
        ));
    }

    public function process(): void
    {
    }

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
        } catch (StorageException $ex) {
            // Add logging when done
            $content = $this->getRendered();
        }
        return $out->setContent($content);
    }

    protected function getRendered(): string
    {
        $tmplMain = new Templates\Main();
        $menu = $this->tree->output('');
        $headerContent = $this->addHeader($menu);
        $inputContent = $this->addInputs($menu);
        return $tmplMain->setData($this->tmplOpen->reset()->setData($headerContent . $inputContent)->render())->render();
    }

    protected function addInputs(?\kalanis\kw_menu\Menu\Menu $menu, string $deepLink = ''): string
    {
        if (empty($menu) || empty($menu->getDisplayCount())) {
            return '';
        }

        $items = $menu->getItems();
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
