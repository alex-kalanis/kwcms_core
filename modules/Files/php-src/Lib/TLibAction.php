<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_confs\Config;
use kalanis\kw_extras\UserDir;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Files\Interfaces;


/**
 * Trait TLibAction
 * @package KWCMS\modules\Files\Lib
 * How process actions over content
 */
trait TLibAction
{
    protected function getLibFileAction(): Interfaces\IProcessFiles
    {
        $userDir = $this->getUserDirLib();
        return new ProcessFile(
            $userDir->getWebRootDir() . $userDir->getHomeDir()
            , Stuff::removeEndingSlash($this->getWhereDir()) . DIRECTORY_SEPARATOR
        );
    }

    protected function getLibDirAction(): Interfaces\IProcessDirs
    {
        $userDir = $this->getUserDirLib();
        return new ProcessDir(
            $userDir->getWebRootDir() . $userDir->getHomeDir()
            , Stuff::removeEndingSlash($this->getWhereDir()) . DIRECTORY_SEPARATOR
        );
    }

    protected function getUserDirLib(): UserDir
    {
        $userDir = new UserDir(Config::getPath());
        $userDir->setUserPath($this->getUserDir());
        $userDir->process();
        return $userDir;
    }

    abstract protected function getUserDir(): string;

    abstract protected function getWhereDir(): string;
}
