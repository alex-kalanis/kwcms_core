<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_confs\Config;
use kalanis\kw_input\Interfaces\IFiltered;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_menu\EntriesSource;
use kalanis\kw_menu\Interfaces;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\MetaProcessor;
use kalanis\kw_menu\MetaSource;
use kalanis\kw_menu\MoreEntries;
use kalanis\kw_semaphore\Interfaces\ISemaphore;
use kalanis\kw_semaphore\Semaphore;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree_controls\TWhereDir;


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
    /** @var MoreEntries|null */
    protected $libMenu = null;
    /** @var ISemaphore|null */
    protected $libSemaphore = null;

    protected function initTMenu(Path $path)
    {
        Config::load('Menu');
        $this->userDir = new UserDir($path);
        $this->libMenu = new MoreEntries($this->initMetaProcessor(), $this->initMenuVolume());
        $this->libSemaphore = $this->initMenuSemaphore();
    }

    protected function initMenuVolume(): Interfaces\IEntriesSource
    {
        return new EntriesSource\Volume($this->userDir->getWebRootDir());
    }

    protected function initMetaProcessor(): MetaProcessor
    {
        $lang = new Translations();
        return new MetaProcessor(new MetaSource\Volume($this->userDir->getWebRootDir(), new MetaSource\FileParser(), $lang), $lang);
    }

    protected function initMenuSemaphore(): ISemaphore
    {
        return new Semaphore\Volume(
            $this->userDir->getWebRootDir() . $this->userDir->getWorkDir() . Config::get('Menu', 'meta_regen'),
            new Translations()
        );
    }

    /**
     * @param IFiltered $inputs
     * @param string $userDir
     * @throws MenuException
     */
    protected function runTMenu(IFiltered $inputs, string $userDir): void
    {
        $this->initWhereDir(new SessionAdapter(), $inputs);
        $this->userDir->setUserPath($userDir);
        $this->userDir->process();

        $this->libMenu->setGroupKey(
            Stuff::removeEndingSlash($this->userDir->getRealDir()) . DIRECTORY_SEPARATOR
            . Stuff::removeEndingSlash($this->getWhereDir()) . DIRECTORY_SEPARATOR
        );
        $this->libMenu->setMeta(
            Stuff::removeEndingSlash($this->userDir->getRealDir()) . DIRECTORY_SEPARATOR
            . Stuff::removeEndingSlash($this->getWhereDir()) . DIRECTORY_SEPARATOR . $this->getMenuMeta()
        );
        $this->libMenu->load();
    }

    protected function getMenuMeta(): string
    {
        return strval(Config::get('Menu', 'meta'));
    }
}
