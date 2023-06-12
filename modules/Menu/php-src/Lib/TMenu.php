<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_input\Interfaces\IFiltered;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\MenuFactory;
use kalanis\kw_menu\MoreEntries;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\Semaphore;
use kalanis\kw_paths\Path;
use kalanis\kw_semaphore\SemaphoreException;
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
    /** @var Access\CompositeAdapter */
    protected $files = null;
    /** @var UserDir */
    protected $userDir = null;

    /**
     * @param Path $path
     * @throws ConfException
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     */
    protected function initTMenu(Path $path)
    {
        Config::load('Menu');
        $this->userDir = new UserDir();
        $this->files = (new Access\Factory())->getClass($path->getDocumentRoot() . $path->getPathToSystemRoot());
        $this->libMenu = (new MenuFactory())->getMenu($this->files);
    }

    /**
     * @param IFiltered $inputs
     * @param string $userDir
     * @throws MenuException
     * @throws PathsException
     * @throws SemaphoreException
     */
    protected function runTMenu(IFiltered $inputs, string $userDir): void
    {
        $this->initWhereDir(new SessionAdapter(), $inputs);
        $this->userDir->setUserPath($userDir);
        $fullPath = array_merge(array_values(
            $this->userDir->process()->getFullPath()->getArray()),
            Stuff::linkToArray($this->getWhereDir())
        );

        // cannot init earlier due need of known user directory
        $this->libSemaphore = (new Semaphore\Factory())->getSemaphore([
            'semaphore' => $this->files,
            'semaphore_root' => array_merge(
                array_values($this->userDir->process()->getFullPath()->getArray()),
                [Config::get('Menu', 'meta_regen')]
            ),
        ]);

        $this->libMenu->setGroupKey($fullPath);
        $this->libMenu->setMeta(array_merge($fullPath, [$this->getMenuMeta()]));
        $this->libMenu->load();
    }

    protected function getMenuMeta(): string
    {
        return strval(Config::get('Menu', 'meta'));
    }
}
