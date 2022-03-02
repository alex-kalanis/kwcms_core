<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_menu\DataSource;
use kalanis\kw_menu\Interfaces;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\MoreFiles;
use kalanis\kw_menu\Semaphore;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree\TWhereDir;


/**
 * Trait TMenu
 * @package KWCMS\modules\Menu\Lib
 * Actions over Menu libraries
 */
trait TMenu
{
    use TWhereDir;

    /** @var UserDir|null */
    protected $userDir = null;
    /** @var MoreFiles|null */
    protected $libMenu = null;
    /** @var Interfaces\ISemaphore|null */
    protected $libSemaphore = null;

    protected function initTMenu(Path $path)
    {
        Config::load('Menu');
        $this->userDir = new UserDir($path);
        $this->libMenu = new MoreFiles( $this->initMenuVolume(), $this->getMenuMeta(), new Translations() );
        $this->libSemaphore = $this->initMenuSemaphore();
    }

    protected function initMenuVolume(): Interfaces\IDataSource
    {
        return new DataSource\Volume($this->userDir->getWebRootDir());
    }

    protected function initMenuSemaphore(): Interfaces\ISemaphore
    {
        return new Semaphore\Volume($this->userDir->getWebRootDir() . $this->userDir->getWorkDir() . Config::get('Menu', 'meta_regen'));
    }

    protected function getMenuMeta(): string
    {
        return strval(Config::get('Menu', 'meta'));
    }

    /**
     * @param IVariables $inputs
     * @param string $userDir
     * @throws MenuException
     */
    protected function runTMenu(IVariables $inputs, string $userDir): void
    {
        $this->initWhereDir(new SessionAdapter(), $inputs);
        $this->userDir->setUserPath($userDir);
        $this->userDir->process();

        $this->libMenu->setPath(
            Stuff::removeEndingSlash($this->userDir->getRealDir()) . DIRECTORY_SEPARATOR
            . Stuff::removeEndingSlash($this->getWhereDir()) . DIRECTORY_SEPARATOR
        );
        $this->libMenu->load();
    }
}
