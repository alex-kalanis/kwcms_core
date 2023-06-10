<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IFiltered;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_menu\EntriesSource;
use kalanis\kw_menu\Interfaces;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\MetaProcessor;
use kalanis\kw_menu\MetaSource;
use kalanis\kw_menu\MoreEntries;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\Semaphore;
use kalanis\kw_paths\Path;
use kalanis\kw_tree\DataSources;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;


/**
 * Trait TMenu
 * @package KWCMS\modules\Menu\Lib
 * Actions over Menu libraries
 */
trait TMenu
{
    use TWhereDir;

    /** @var MoreEntries */
    protected $libMenu = null;
    /** @var ISemaphore */
    protected $libSemaphore = null;
    /** @var ITree */
    protected $tree = null;
    /** @var UserDir */
    protected $userDir = null;

    /**
     * @param Path $path
     * @throws ConfException
     */
    protected function initTMenu(Path $path)
    {
        Config::load('Menu');
        $this->tree = new DataSources\Volume($path->getDocumentRoot() . $path->getPathToSystemRoot());
        $this->userDir = new UserDir();
        $this->libMenu = new MoreEntries($this->initMetaProcessor($path), $this->initMenuVolume($path));
        $this->libSemaphore = $this->initMenuSemaphore($path);
    }

    protected function initMenuVolume(Path $path): Interfaces\IEntriesSource
    {
        return new EntriesSource\Volume($path->getDocumentRoot() . $path->getPathToSystemRoot() . DIRECTORY_SEPARATOR);
    }

    protected function initMetaProcessor(Path $path): MetaProcessor
    {
        $lang = new Translations();
        return new MetaProcessor(new MetaSource\Volume($path->getDocumentRoot() . $path->getPathToSystemRoot() . DIRECTORY_SEPARATOR, new MetaSource\FileParser(), $lang), $lang);
    }

    protected function initMenuSemaphore(Path $path): ISemaphore
    {
        return new Semaphore\Volume(
            $path->getDocumentRoot() . $path->getPathToSystemRoot() . Config::get('Menu', 'meta_regen'),
            new Translations()
        );
    }

    /**
     * @param IFiltered $inputs
     * @param string $userDir
     * @throws MenuException
     * @throws PathsException
     */
    protected function runTMenu(IFiltered $inputs, string $userDir): void
    {
        $this->initWhereDir(new SessionAdapter(), $inputs);
        $this->userDir->setUserPath($userDir);
        $fullPath = array_merge(array_values(
            $this->userDir->process()->getFullPath()->getArray()),
            Stuff::linkToArray($this->getWhereDir())
        );
        $this->libMenu->setGroupKey($fullPath);
        $this->libMenu->setMeta(array_merge($fullPath, [$this->getMenuMeta()]));
        $this->libMenu->load();
    }

    protected function getMenuMeta(): string
    {
        return strval(Config::get('Menu', 'meta'));
    }
}
