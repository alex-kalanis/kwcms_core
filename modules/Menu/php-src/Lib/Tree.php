<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_confs\Config;
use kalanis\kw_menu\EntriesSource;
use kalanis\kw_menu\Menu\Entry;
use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\MetaProcessor;
use kalanis\kw_menu\MoreEntries;
use kalanis\kw_menu\MetaSource;
use kalanis\kw_modules\Linking\InternalLink;
use kalanis\kw_paths\Stuff;


/**
 * Class Tree
 * @package KWCMS\modules\Menu\Lib
 * Menu tree
 */
class Tree
{
    protected $link = null;
    /** @var MoreEntries|null */
    protected $processor = null;

    public function __construct(InternalLink $link)
    {
        // set path from link is a bit aggressive, so do not set real volume path in advance
        $this->link = $link;
    }

    public function output(string $startPath): ?Menu
    {
        $path = $this->link->userContent($startPath, true, false);
        try {
            $lang = new Translations();
            $this->processor = new MoreEntries(
                new MetaProcessor(new MetaSource\Volume($path, new MetaSource\FileParser(), $lang), $lang),
                new EntriesSource\Volume($path)
            );
            $this->processor->setMeta(strval(Config::get('Menu','meta')));
            $menu = $this->processor->load()->getMeta()->getMenu();
        } catch (MenuException $ex) {
            return null;
        }
        foreach ($menu->getEntries() as $item) {
            $this->loadSub($item, '');
        }
        return $menu;
    }

    protected function loadSub(Entry $item, string $deepLink): void
    {
        if (!$item->canGoSub()) {
            return;
        }
        $localPath = $deepLink . DIRECTORY_SEPARATOR . Stuff::fileBase($item->getName());
        try {
            $this->processor->setGroupKey($localPath);
            $this->processor->setMeta($localPath . DIRECTORY_SEPARATOR . strval(Config::get('Menu','meta')));
            $menu = $this->processor->load()->getMeta()->getMenu();
            if (!empty($menu)) {
                foreach ($menu->getEntries() as $item) {
                    $this->loadSub($item, $localPath);
                }
                $item->addSubmenu($menu);
            }
        } catch (MenuException $ex) {
            // pass
        }
    }
}
