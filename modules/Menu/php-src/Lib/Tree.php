<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_confs\Config;
use kalanis\kw_menu\Menu\Entry;
use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\MoreEntries;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_user_paths\UserInnerLinks;


/**
 * Class Tree
 * @package KWCMS\modules\Menu\Lib
 * Menu tree
 */
class Tree
{
    /** @var MoreEntries|null */
    protected $processor = null;
    /** @var ITree */
    protected $tree = null;
    /** @var UserInnerLinks */
    protected $innerLink = null;

    public function __construct(MoreEntries $processor, UserInnerLinks $innerLink)
    {
        // set path from link is a bit aggressive, so do not set real volume path in advance
        $this->processor = $processor;
        $this->innerLink = $innerLink;
    }

    /**
     * @param string[] $startPath
     * @return Menu|null
     */
    public function output(array $startPath): ?Menu
    {
        try {
            $path = $this->innerLink->toFullPath($startPath);
            $this->processor->setGroupKey($path);
            $this->processor->setMeta(array_merge($path, [strval(Config::get('Menu','meta'))]));
            $menu = $this->processor->load()->getMeta()->getMenu();
        } catch (MenuException | PathsException $ex) {
            return null;
        }
        foreach ($menu->getEntries() as $item) {
            $this->loadSub($item, $path);
        }
        return $menu;
    }

    /**
     * @param Entry $item
     * @param string[] $deepLink
     */
    protected function loadSub(Entry $item, array $deepLink): void
    {
        if (!$item->canGoSub()) {
            return;
        }
        try {
            $localPath = array_merge($deepLink, [Stuff::fileBase($item->getName())]);
            $this->processor->setGroupKey($localPath);
            $this->processor->setMeta(array_merge($localPath, [strval(Config::get('Menu','meta'))]));
            $menu = $this->processor->load()->getMeta()->getMenu();
            if (!empty($menu)) {
                foreach ($menu->getEntries() as $item) {
                    $this->loadSub($item, $localPath);
                }
                $item->addSubmenu($menu);
            }
        } catch (MenuException | PathsException $ex) {
            // pass
        }
    }
}
