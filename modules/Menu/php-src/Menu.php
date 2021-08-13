<?php

namespace KWCMS\modules\Menu;


use kalanis\kw_confs\Config;
use kalanis\kw_extras\Cache;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_storage\Storage;


/**
 * Class Menu
 * @package KWCMS\modules\Menu
 * Menu included as module
 */
class Menu extends AModule
{
    protected $canCache = false;
    protected $cache = null;
    protected $link = null;
    protected $tree = null;
    protected $tmplOpen = null;
    protected $tmplDisplay = null;

    public function __construct()
    {
        Config::load('Menu');
        $cachePath = Config::getPath()->getDocumentRoot() . DIRECTORY_SEPARATOR . IPaths::DIR_TEMP;
        $this->canCache = (bool)Config::get('Menu', 'use_cache', false);
        Storage\Key\DirKey::setDir($cachePath. DIRECTORY_SEPARATOR);
        $storage = new Storage(new Storage\Factory(new Storage\Target\Factory(), new Storage\Format\Factory(), new Storage\Key\Factory()));
        $storage->init('volume');
        $this->cache = new Cache($storage);
        $this->cache->init('Menu');
        $this->link = new ExternalLink(Config::getPath());
        $this->tree = new Tree(Config::getPath());
        $this->tmplOpen = new Templates\Open();
        $this->tmplDisplay = new Templates\Display();
    }

    public function process(): void
    {
    }

    public function output(): AOutput
    {
        $out = new Html();
        $content = $this->canCache && $this->cache->isAvailable() ? $this->cache->get() : $this->getRendered();
        if ($this->canCache && !$this->cache->isAvailable()) {
            $this->cache->save($content);
        }
        return $out->setContent($content);
    }

    protected function getRendered(): string
    {
        $tmplMain = new Templates\Main();
        $menu = $this->tree->output(
            (bool)Config::get('Core', 'page.more_lang')
                ? ( Config::getPath()->getLang() ?: Lang::getLang() )
                : ''
        );
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
                    $this->link->linkVariant($linkPath),
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
