<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_confs\Config;
use kalanis\kw_images\FilesHelper;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Images\Interfaces;


/**
 * Trait TLibAction
 * @package KWCMS\modules\Images\Lib
 * How process actions over content
 */
trait TLibAction
{
    protected function getLibFileAction(): Interfaces\IProcessFiles
    {
        $userDir = new UserDir(Config::getPath());
        $userDir->setUserPath($this->getUserDir());
        $userDir->process();
        return new ProcessFile(
            FilesHelper::get($userDir->getWebRootDir() . $userDir->getHomeDir()),
            Stuff::sanitize($this->getWhereDir()) . DIRECTORY_SEPARATOR
        );
    }

    protected function getLibDirAction(): Interfaces\IProcessDirs
    {
        $userDir = new UserDir(Config::getPath());
        $userDir->setUserPath($this->getUserDir());
        $userDir->process();
        return new ProcessDir(
            FilesHelper::get($userDir->getWebRootDir() . $userDir->getHomeDir()),
            Stuff::sanitize($this->getWhereDir()) . DIRECTORY_SEPARATOR
        );
    }

    abstract protected function getUserDir(): string;

    abstract protected function getWhereDir(): string;
}
