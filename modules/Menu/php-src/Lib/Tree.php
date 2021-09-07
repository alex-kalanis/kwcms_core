<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_menu\Menu\Item;
use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\MoreFiles;
use kalanis\kw_modules\InternalLink;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\Stuff;


/**
 * Class Tree
 * @package KWCMS\modules\Menu\Lib
 * Menu tree
 */
class Tree
{
    protected $link = null;
    protected $processor = null;

    public function __construct(Path $path)
    {
        $this->processor = new MoreFiles();
        $this->link = new InternalLink($path);
    }

    public function output(string $startPath): ?Menu
    {
        try {
            $menu = $this->processor->setPath($this->link->userContent($startPath, false, false))->getMenu();
        } catch (MenuException $ex) {
            return null;
        }
        foreach ($menu->getItems() as $item) {
            $this->loadSub($item, $startPath);
        }
        return $menu;
    }

    protected function loadSub(Item $item, string $deepLink): void
    {
        if (!$item->canGoSub()) {
            return;
        }
        $localPath = $deepLink . DIRECTORY_SEPARATOR . Stuff::fileBase($item->getName());
        try {
            $menu = $this->processor->setPath($this->link->userContent($localPath, false, false))->getMenu();
            if (!empty($menu)) {
                foreach ($menu->getItems() as $item) {
                    $this->loadSub($item, $localPath);
                }
                $item->addSubmenu($menu);
            }
        } catch (MenuException $ex) {
            // pass
        }
    }
}
